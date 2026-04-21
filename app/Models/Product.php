<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;


class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'price', 'price_sale', 'category', 'weight', 'image', 'images', 'is_featured', 'is_active'];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'price_sale' => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────
    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    // ── Accessor ────────────────────────────────────────
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.png');
    }

    public function getTotalStockAttribute(): int
    {
        return $this->sizes->sum('stock');
    }

    public function getDisplayPriceAttribute(): string
    {
        return 'Rp' . number_format((float) $this->price, 0, ',', '.');
    }

    public function getDisplaySalePriceAttribute(): ?string
    {
        return $this->price_sale ? 'Rp' . number_format((float) $this->price_sale, 0, ',', '.') : null;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->price_sale || $this->price_sale >= $this->price) {
            return null;
        }
        return (int) round((($this->price - $this->price_sale) / $this->price) * 100);
    }

    // ── Mutator / Boot ──────────────────────────────────
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name) . '-' . uniqid();
        });
    }

    // ── Scope ───────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
