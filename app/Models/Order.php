<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',

        // Penerima
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'receiver_province',
        'receiver_city',
        'receiver_district',
        'receiver_village',
        'receiver_postal_code',

        // Kurir
        'courier_code',
        'courier_name',
        'courier_service',
        'shipping_cost',
        'shipping_estimate',
        'tracking_number',

        // Keuangan
        'subtotal',
        'total_amount',

        // Pembayaran
        'payment_proof',
        'paid_at',

        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'integer',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ── Relasi ───────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessor ─────────────────────────────────────────────
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function getFormattedShippingAttribute(): string
    {
        return 'Rp' . number_format((float) $this->shipping_cost, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'waiting_confirmation' => 'blue',
            'confirmed' => 'indigo',
            'processing' => 'purple',
            'shipped' => 'cyan',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    // ── Scope ────────────────────────────────────────────────
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
