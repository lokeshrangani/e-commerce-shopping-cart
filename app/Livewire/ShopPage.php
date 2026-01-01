<?php

namespace App\Livewire;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Service\CartService;
use App\Service\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Shop')]
class ShopPage extends Component
{
    public $cart;

    public $canCheckout = false;

    public function mount()
    {
        $this->cart = (new CartService)->getCart(Auth::user());
        $this->canCheckout = $this->cart && $this->cart->items->isNotEmpty();
    }

    public function addToCart(int $productId): void
    {
        try {
            (new CartService)->addProduct(Auth::user(), $productId);
            $this->refreshCart();
        } catch (\Exception $e) {
            Log::error('Error adding product to cart: ' . $e->getMessage());
            $this->dispatch('show-alert', message: $e->getMessage());
        }
    }

    public function decrementQuantity(int $productId): void
    {
        try {
            (new CartService)->decrementQuantity(Auth::user(), $productId);
        } catch (\Exception $e) {
            Log::error('Error decrementing quantity: ' . $e->getMessage());
            $this->dispatch('show-alert', message: 'Error decrementing quantity');
        }
    }

    public function removeItem(int $itemId): void
    {
        (new CartService)->removeItem(Auth::user(), $itemId);
    }

    public function checkout(): void
    {
        try {
            $order = (new OrderService)->checkout(Auth::user());
            $this->dispatch('show-alert', message: "Order {$order->id} created successfully");
        } catch (InsufficientStockException $e) {
            Log::error('Insufficient stock: ' . $e->getMessage());
            $this->dispatch('show-alert', message: $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error during checkout: ' . $e->getMessage());
            $this->dispatch('show-alert', message: 'Error during checkout');
        }
    }

    protected function refreshCart(): void
    {
        $this->cart = (new CartService)->getCart(Auth::user());
        $this->canCheckout = $this->cart && $this->cart->items->isNotEmpty();
    }

    public function render()
    {
        if (! Auth::check()) {
            return;
        }

        return view('livewire.shop-page', [
            'products' => Product::where('stock_quantity', '>', 0)->get(),
        ]);
    }
}
