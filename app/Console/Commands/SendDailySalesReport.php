<?php

namespace App\Console\Commands;

use App\Mail\DailySalesMail;
use App\Service\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport extends Command
{
    protected $signature = 'report:daily-sales {date? : The date to send the report for (YYYY-MM-DD)}';

    protected $description = 'Send daily sales report';

    public function handle()
    {
        $date = $this->argument('date')
            ? Carbon::parse($this->argument('date'))
            : now();

        $orders = (new OrderService())->getOrdersByDate($date->toDateString());

        $adminEmail = config('app.admin_email', 'admin@test.com');

        Mail::to($adminEmail)->send(
            new DailySalesMail($orders, $orders->sum('total_amount'), $orders->count(), $date->toDateString())
        );

        $this->info('Daily sales report sent.');

        return Command::SUCCESS;
    }
}
