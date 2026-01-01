<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property float $price
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'stock_quantity', 'low_stock_threshold'];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
