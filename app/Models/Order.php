<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_code', 'user_id', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_province', 'receiver_city', 'receiver_postal_code', 'courier_name', 'courier_service', 'shipping_cost', 'shipping_estimate', 'subtotal', 'total', 'status', 'payment_method', 'payment_proof', 'paid_at', 'notes', 'cancelled_at', 'cancel_reason', 'cancelled_by', 'payment_deadline'];

    protected $casts = [
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_deadline' => 'datetime',
    ];

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

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['pending', 'paid']) && $this->status !== 'cancelled';
    }

    // Apakah masih bisa upload bukti bayar?
    public function getCanPayAttribute(): bool
    {
        return $this->status === 'pending' && !$this->isPaymentExpired();
    }

    public function isPaymentExpired(): bool
    {
        if (!$this->payment_deadline) {
            return false;
        }
        return now()->isAfter($this->payment_deadline);
    }

    public function getPaymentSecondsLeftAttribute(): int
    {
        if (!$this->payment_deadline) {
            return 0;
        }
        $diff = now()->diffInSeconds($this->payment_deadline, false);
        return max(0, $diff);
    }

    public function getPaymentDeadlineLabelAttribute(): string
    {
        if (!$this->payment_deadline) {
            return '-';
        }
        return $this->payment_deadline->isoFormat('D MMM Y, HH:mm') . ' WIB';
    }
}
