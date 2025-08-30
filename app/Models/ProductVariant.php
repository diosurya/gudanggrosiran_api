<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer'
    ];

    protected $attributes = [
        'stock' => 0,
    ];

    // Relationships

    /**
     * Get the product that owns the variant
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_variant_media');
    }


    /**
     * Get the primary image for the variant
     */
    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }


    // Scopes

    /**
     * Scope a query to only include in-stock variants
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock > 10) {
            return 'in_stock';
        } elseif ($this->stock > 0) {
            return 'low_stock';
        } else {
            return 'out_of_stock';
        }
    }

    /**
     * Scope a query to only include active variants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}