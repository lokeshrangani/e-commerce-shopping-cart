<?php

namespace App\Jobs;

use App\Mail\LowStockMail;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class LowStockJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('app.admin_email', 'admin@test.com');

        Mail::to($adminEmail)
            ->send(new LowStockMail($this->product));
    }
}
