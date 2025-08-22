<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use HasUuids;

    protected $table = 'blog_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Self-referencing relationship - Parent Category
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    // Self-referencing relationship - Child Categories
    public function children(): HasMany
    {
        return $this->hasMany(BlogCategory::class, 'parent_id')->orderBy('sort_order');
    }

    // Blogs in this category
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
