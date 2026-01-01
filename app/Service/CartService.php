<?php

namespace App\Service;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(User $user)
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        )->load('items.product');
    }

    public function addProduct(User $user, int $productId, int $qty = 1)
    {
        return DB::transaction(function () use ($user, $productId, $qty) {
            $cart = $this->getCart($user);

            $cartItem = $cart?->items->firstWhere('product_id', $productId);

            $product = Product::findOrFail($productId);

            if ($cartItem) {
                // Only increment if stock allows
                $newQuantity = min($cartItem->quantity + $qty, $product->stock_quantity);

                if ($newQuantity == $cartItem->quantity) {
                    throw new InsufficientStockException("Cannot add more of {$product->name}, stock limit reached.");
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                if ($product->stock_quantity < $qty) {
                    throw new InsufficientStockException("Cannot add {$qty} of {$product->name}, only {$product->stock_quantity} available.");
                }

                $cartItem = $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $qty,
                ]);
            }

            activity_log(
                $user,
                'cart.added',
                [
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'new_quantity' => $cartItem->quantity,
                ]
            );

            return $cartItem;
        });
    }

    public function decrementQuantity(User $user, int $itemId, int $qty = 1)
    {
        $item = CartItem::query()
            ->where('id', $itemId)
            ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$item) {
            throw new \Exception('Item not found in cart');
        }

        if ($item->quantity <= $qty) {
            activity_log(
                $user,
                'cart.removed',
                [
                    'product_id' => $item->product_id,
                    'quantity' => $qty,
                    'new_quantity' => $item->quantity,
                ]
            );

            $item->delete();
            return;
        }

        $item->decrement('quantity', $qty);

        activity_log(
            $user,
            'cart.decremented',
            [
                'product_id' => $item->product_id,
                'quantity' => $qty,
                'new_quantity' => $item->quantity,
            ]
        );
    }

    public function removeItem(User $user, int $itemId)
    {
        $item = CartItem::query()
            ->where('id', $itemId)
            ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$item) {
            throw new \Exception('Item not found in cart');
        }

        activity_log(
            $user,
            'cart.removed',
            [
                'product_id' => $item->product_id,
            ]
        );

        $item->delete();
    }
}
