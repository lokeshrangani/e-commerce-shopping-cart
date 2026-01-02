<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Livewire\ShopPage;
use App\Models\Product;
use App\Models\User;
use App\Service\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock_quantity' => 5,
            'price' => 500,
            'low_stock_threshold' => 2,
        ]);

        $cartService = new CartService;
        $cartService->addProduct($user, $product->id, 1);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'cart_id' => $user->cart->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'action' => 'cart.added',
        ]);
    }

    public function test_add_product_throws_exception_if_stock_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 2]);

        $cartService = new CartService;
        $cartService->addProduct($user, $product->id, 1);
        $this->assertEquals(1, $user->cart->items()->first()->quantity);

        $cartService->addProduct($user, $product->id, 1);
        $this->assertEquals(2, $user->cart->items()->first()->quantity);

        $this->expectException(InsufficientStockException::class);
        $cartService->addProduct($user, $product->id, 1);
    }

    public function test_decrement_removes_item_if_quantity_reaches_zero()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $cartService = new CartService;
        $cartService->addProduct($user, $product->id, 1);

        $cartService->decrementQuantity($user, $user->cart->items()->first()->id, 5);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'action' => 'cart.removed',
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_livewire_add_to_cart_component()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 3]);

        Livewire::actingAs($user)
            ->test(ShopPage::class)
            ->call('addToCart', $product->id)
            ->assertSee('Your Cart')
            ->assertSee($product->name)
            ->assertSee('1');
    }
}
