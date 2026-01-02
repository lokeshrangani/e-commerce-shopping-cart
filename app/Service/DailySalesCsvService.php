<?php

namespace App\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DailySalesCsvService
{
    public function generate(Collection $orders, string $date): string
    {
        $directory = 'reports';
        $filename = "daily-sales-{$date}.csv";
        $path = "{$directory}/{$filename}";

        Storage::makeDirectory($directory);

        $handle = fopen(storage_path("app/private/$path"), 'w');
        fputcsv($handle, [
            'Order ID',
            'Product',
            'Quantity',
            'Price',
            'Order Total',
            'Order Date',
        ]);

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                fputcsv($handle, [
                    $order->id,
                    $item->product->name,
                    $item->quantity,
                    $item->price_at_purchase,
                    $order->total_amount,
                    $order->created_at->toDateTimeString(),
                ]);
            }
        }

        fclose($handle);

        return $path;
    }

    public function download(string $filename)
    {
        $path = "reports/{$filename}";

        abort_unless(Storage::exists($path), 404);

        return Storage::download(
            $path,
            $filename,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }

    public function getSignedUrl(string $fileName): string
    {
        return URL::signedRoute(
            'reports.download',
            ['filename' => basename($fileName)]
        );
    }

    public function prepareDailySalesData(string $date): array
    {
        $orders = (new OrderService)->getOrdersByDate($date);

        $reportService = new DailySalesCsvService;
        $csvPath = $reportService->generate($orders, $date);
        $downloadUrl = $reportService->getSignedUrl($csvPath);

        return [
            'downloadUrl' => $downloadUrl,
            'total' => $orders->sum('total_amount'),
            'orderCount' => $orders->count(),
            'date' => $date,
        ];
    }
}
