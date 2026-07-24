<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['user_id', 'sender', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /** Pesan yang masih terlihat di sisi user (belum dihapus user) */
    public function scopeVisibleToUser($query)
    {
        return $query->whereNull('deleted_by_user_at');
    }

    /** Pesan yang masih terlihat di sisi admin (belum dihapus admin) */
    public function scopeVisibleToAdmin($query)
    {
        return $query->whereNull('deleted_by_admin_at');
    }
}
