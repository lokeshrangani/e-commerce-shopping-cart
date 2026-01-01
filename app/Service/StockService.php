<?php

namespace App\Service;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Jobs\LowStockJob;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function decrementStock(int $productId, int $qty): Product
    {
        return DB::transaction(function () use ($productId, $qty) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($product->stock_quantity < $qty) {
                throw new InsufficientStockException("Insufficient stock", 422);
            }

            $product->decrement('stock_quantity', $qty);

            if ($product->stock_quantity <= $product->low_stock_threshold) {
                LowStockJob::dispatch($product);
            }

            return $product;
        });
    }

    public function getProduct(int $productId): Product
    {
        return Product::withCount('cartItems')->findOrFail($productId);
    }
}
