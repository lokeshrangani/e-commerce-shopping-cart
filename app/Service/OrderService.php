<?php

namespace App\Service;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected CartService $cartService = new CartService,
        protected StockService $stockService = new StockService,
    ) {}

    public function checkout(User $user): Order
    {
        return DB::transaction(function () use ($user) {

            $cart = $this->cartService->getCart($user);

            if (! $cart || $cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $total = 0;

            // Validate stock and calculate total
            foreach ($cart->items as $item) {
                if ($item->quantity > $item->product->stock_quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for {$item->product->name}",
                        422
                    );
                }
                $total += $item->quantity * $item->product->price;
            }

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
            ]);

            // Create OrderItems & reduce stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->price,
                ]);

                $this->stockService->decrementStock(
                    $item->product_id,
                    $item->quantity
                );
            }

            // Clear cart items
            $cart->items()->delete();

            activity_log(
                $user,
                'order.placed',
                [
                    'order_id' => $order->id,
                    'total_amount' => $total,
                ]
            );

            return $order;
        });
    }

    public function getOrders(User $user): Collection
    {
        return $user->orders()->with('items.product')->get();
    }

    public function getOrdersByDate(string $date): Collection
    {
        return Order::with(['items.product'])
            ->whereDate('created_at', $date)
            ->get();
    }
}
