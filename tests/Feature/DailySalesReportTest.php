<?php

namespace Tests\Feature;

use App\Mail\DailySalesMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_sales_report_sends_email()
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'test@admin.com',
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 500]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
            'total_amount' => 1000,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => 500,
        ]);

        $this->artisan('report:daily-sales')
            ->assertExitCode(0);

        Mail::assertSent(DailySalesMail::class, function ($mail) {
            return $mail->orders->count() === 1;
        });
    }

    public function test_daily_sales_report_handles_no_orders()
    {
        Mail::fake();

        $this->artisan('report:daily-sales')
            ->assertExitCode(0);

        Mail::assertSent(DailySalesMail::class, function ($mail) {
            return $mail->orders->isEmpty() === true;
        });
    }
}
