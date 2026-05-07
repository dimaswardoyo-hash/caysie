<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $fillable = ['awb', 'courier', 'status', 'history', 'delivery_status', 'manifest', 'last_checked_at'];

    protected $casts = [
        'history' => 'array',
        'delivery_status' => 'array',
        'manifest' => 'array',
        'last_checked_at' => 'datetime',
    ];

    // Scope untuk pencarian
    public function scopeByAwb($query, $awb)
    {
        return $query->where('awb', $awb);
    }

    // Update status terbaru
    public function updateStatus(array $data)
    {
        $this->update([
            'status' => $data['status'] ?? $this->status,
            'history' => $data['history'] ?? $this->history,
            'delivery_status' => $data['delivery_status'] ?? $this->delivery_status,
            'manifest' => $data['manifest'] ?? $this->manifest,
            'last_checked_at' => now(),
        ]);
    }
}
