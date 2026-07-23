<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'product_size', 'product_image', 'quantity', 'price', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke produk asli — dipakai untuk ambil berat produk (cek ongkir,
    // generate resi Biteship) dan tampilan detail pesanan di admin.
    // Nullable karena produk bisa saja sudah dihapus setelah order dibuat
    // (product_name/product_size/product_image di order_items adalah snapshot).
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
