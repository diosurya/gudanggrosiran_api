<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Blog extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'reading_time',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'status',
        'author_id',
        'view_count',
        'share_count',
        'average_rating',
        'published_at',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'reading_time' => 'integer',
        'view_count' => 'integer',
        'share_count' => 'integer',
        'average_rating' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    // Category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    // Author relationship
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Blog images
    public function images(): HasMany
    {
        return $this->hasMany(BlogImage::class, 'blog_id')->orderBy('sort_order');
    }

    // Cover image
    public function coverImage(): HasOne
    {
        return $this->hasOne(BlogImage::class, 'blog_id')->where('is_cover', true);
    }

    // Tags (many-to-many)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_tag');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('published_at', '>', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }
}
