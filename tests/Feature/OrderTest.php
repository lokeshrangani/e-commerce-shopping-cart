<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Jobs\LowStockJob;
use App\Models\Product;
use App\Models\User;
use App\Service\CartService;
use App\Service\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_and_clears_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $cartService = new CartService();
        $cartService->addProduct($user, $product->id, 2);

        $orderService = new OrderService();
        $order = $orderService->checkout($user);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertEquals(3, $product->fresh()->stock_quantity);
        $this->assertEmpty($user->cart->items);
    }

    // it actually tests the add product method
    public function test_checkout_throws_exception_if_stock_insufficient()
    {
        $this->expectException(InsufficientStockException::class);

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 1]);

        $cartService = new CartService();
        $cartService->addProduct($user, $product->id, 2); // Should fail

        $orderService = new OrderService();
        $orderService->checkout($user->id);
    }

    public function test_low_stock_job_dispatched_on_checkout()
    {
        Queue::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5, 'low_stock_threshold' => 3]);

        $cartService = new CartService();
        $cartService->addProduct($user, $product->id, 3);

        $orderService = new OrderService();
        $orderService->checkout($user);

        Queue::assertPushed(LowStockJob::class);
    }
}
