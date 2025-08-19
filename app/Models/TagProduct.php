<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagProduct extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
