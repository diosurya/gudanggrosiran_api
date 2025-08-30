<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Page extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'slug',
        'banner_image_id',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image_id',
        'twitter_image_id',
        'canonical_url',
        'robots',
        'is_published',
        'published_at',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    // Relationships
    public function bannerImage()
    {
        return $this->belongsTo(Media::class, 'banner_image_id');
    }

}
