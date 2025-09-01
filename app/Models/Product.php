<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'featured' => 'boolean',
        'stock_quantity' => 'integer',
        'views_count' => 'integer',
        'sales_count' => 'integer'
    ];


    // Relationships

    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }

    /**
     * Get the brand that owns the product
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the tags for the product
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    /**
     * Get the media/images for the product
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'product_media')
                    ->withTimestamps()
                    ->orderBy('product_media.created_at');
    }

    /**
     * Get the variants for the product
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the primary image for the product
     */
    public function primaryImage()
    {
        return $this->belongsToMany(Media::class, 'product_media')
                    ->withTimestamps()
                    ->orderBy('product_media.created_at')
                    ->limit(1);
    }

    /**
     * Get the OG image
     */
    public function ogImage()
    {
        return $this->belongsTo(Media::class, 'og_image', 'id');
    }

    /**
     * Get the Twitter image
     */
    public function twitterImage()
    {
        return $this->belongsTo(Media::class, 'twitter_image', 'id');
    }

    // Accessors & Mutators

    /**
     * Get the product's discounted price
     */
    public function getDiscountedPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Get the product's discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Check if product is in stock
     */
    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if product has discount
     */
    public function getHasDiscountAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    // Scopes

    /**
     * Scope a query to only include published products
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to search products
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_product_id', $categoryId);
    }

    /**
     * Scope a query to filter by brand
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope a query to filter by price range
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'id');
    }
}
