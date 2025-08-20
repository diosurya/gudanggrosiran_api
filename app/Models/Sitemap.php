<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'path',
        'url_count',
        'last_generated',
    ];

    protected $casts = [
        'url_count' => 'integer',
        'last_generated' => 'datetime',
    ];

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_generated', 'desc');
    }
}
