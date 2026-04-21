<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'product_size_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute(): float
    {
        $price = $this->product->price_sale ?? $this->product->price;
        return $price * $this->quantity;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp' . number_format($this->subtotal, 0, ',', '.');
    }
}
