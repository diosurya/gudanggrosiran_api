<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Store extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'stores';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name','slug','address','city','province','postal_code',
        'phone','latitude','longitude','is_active','meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id', 'id');
    }
}
