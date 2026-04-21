<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_code', 'user_id', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_province', 'receiver_city', 'receiver_postal_code', 'courier_name', 'courier_service', 'shipping_cost', 'shipping_estimate', 'subtotal', 'total', 'status', 'payment_method', 'payment_proof', 'paid_at', 'notes'];

    protected $casts = ['paid_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending' => 'yellow',
            'paid' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
        ][$this->status] ?? 'gray';
    }
}
