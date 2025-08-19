<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'username', 'email', 'avatar_url', 'bio'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
