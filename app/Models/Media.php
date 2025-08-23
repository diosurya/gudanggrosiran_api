<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory, HasUuids;

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'name',
        'filename',
        'path',
        'size',
        'type',
        'extension',
        'width',
        'height',
        'alt_text',
        'description'
    ];

     protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

     protected $appends = [
        'full_url',
        'formatted_size',
        'is_image',
        'dimensions'
    ];

    public function getFullUrlAttribute()
    {
        return url($this->path);
    }


    public function getFormattedSizeAttribute()
    {
        return $this->formatBytes($this->size);
    }

  
    public function getIsImageAttribute()
    {
        return strpos($this->type, 'image/') === 0;
    }

    public function getDimensionsAttribute()
    {
        if ($this->width && $this->height) {
            return $this->width . ' x ' . $this->height . ' px';
        }
        return null;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes == 0) {
            return '0 B';
        }

        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Get media by extension
     */
    public function scopeByExtension($query, $extension)
    {
        return $query->where('extension', $extension);
    }

    /**
     * Recent uploads (last 7 days)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    

}
