<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'title',
        'slug',
        'sku',
        'excerpt',
        'description',
        'specifications',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'price',
        'discount_price',
        'cost_price',
        'stock',
        'min_stock',
        'track_stock',
        'allow_backorder',
        'weight',
        'length',
        'width',
        'height',
        'shipping_class_id',
        'is_featured',
        'is_digital',
        'is_downloadable',
        'requires_shipping',
        'status',
        'visibility',
        'password',
        'average_rating',
        'review_count',
        'view_count',
        'purchase_count',
        'published_at',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'is_downloadable' => 'boolean',
        'requires_shipping' => 'boolean',
        'track_stock' => 'boolean',
        'allow_backorder' => 'boolean',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'review_count' => 'integer',
        'view_count' => 'integer',
        'purchase_count' => 'integer',
        'average_rating' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    // Category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Subcategory relationship
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class, 'subcategory_id');
    }

    // Brand relationship
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Product variants
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // Product images
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order');
    }

    // Cover image
    public function coverImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_cover', true);
    }

    // Product reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    // Approved reviews
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id')->where('is_approved', true);
    }

    // Product attribute values
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    // Tags (many-to-many)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('visibility', 'public')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }
}
