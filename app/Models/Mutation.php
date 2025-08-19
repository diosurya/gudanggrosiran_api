<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function akunPenerima()
    {
        return $this->belongsTo(Akun::class, 'id_penerima', 'id');
    }
    public function akunPengirim()
    {
        return $this->belongsTo(Akun::class, 'id_pengirim', 'id');
    }
}
