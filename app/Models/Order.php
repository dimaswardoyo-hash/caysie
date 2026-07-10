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
        'payment_deadline',
        'payment_method',
        'payment_channel',

        // Xendit
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_expires_at',

        // Pembatalan
        'cancelled_at',
        'cancel_reason',
        'cancelled_by',

        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'integer',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'xendit_expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    // Alias tampilan untuk order_number (dipakai di banyak view)
    public function getOrderCodeAttribute(): string
    {
        return $this->order_number;
    }

    // Alias numerik untuk total_amount (dipakai untuk number_format di view)
    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp' . number_format((float) $this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingAttribute(): string
    {
        return 'Rp' . number_format((float) $this->shipping_cost, 0, ',', '.');
    }

    // Pesanan masih bisa dibatalkan selama belum diproses/selesai/dibatalkan
    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['pending', 'waiting_confirmation']);
    }

    // Pesanan masih bisa dibayar: masih pending & belum kedaluwarsa
    public function getCanPayAttribute(): bool
    {
        return $this->status === 'pending' && !$this->isPaymentExpired();
    }

    public function isPaymentExpired(): bool
    {
        if ($this->status !== 'pending' || !$this->payment_deadline) {
            return false;
        }

        return now()->greaterThan($this->payment_deadline);
    }

    public function getPaymentSecondsLeftAttribute(): int
    {
        if (!$this->payment_deadline || $this->isPaymentExpired()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->payment_deadline, false));
    }

    public function getPaymentDeadlineLabelAttribute(): string
    {
        if (!$this->payment_deadline) {
            return '';
        }

        return $this->isPaymentExpired() ? 'Batas waktu pembayaran telah berakhir' : 'Bayar sebelum ' . $this->payment_deadline->isoFormat('D MMMM Y, HH:mm');
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

    // ── Kembalikan stok semua item pesanan ini ────────────────
    // Dipakai oleh SEMUA jalur pembatalan (user cancel, auto-expire,
    // webhook Xendit expired, admin ubah status ke cancelled) agar
    // stok selalu konsisten dikembalikan, di mana pun pembatalan terjadi.
    public function restoreStock(): void
    {
        foreach ($this->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if (!$product) {
                continue;
            }

            $size = $product->sizes()->where('size', $item->product_size)->lockForUpdate()->first();
            if ($size) {
                $size->increment('stock', $item->quantity);
            }
        }
    }
}
