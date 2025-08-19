<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostStat extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'views', 'likes', 'shares'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
