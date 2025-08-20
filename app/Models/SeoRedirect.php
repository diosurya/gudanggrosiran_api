<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    use HasUuids;

    protected $fillable = [
        'old_url',
        'new_url',
        'status_code',
        'hits',
        'is_active',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hits' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('hits', 'desc');
    }
}
