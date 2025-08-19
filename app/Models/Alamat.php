<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    use HasFactory;

    protected $fillable = ['id_customer', 'nama_penerima', 'nama_alamat', 'detail_alamat', 'no_hp', 'kode_pos'];

    /**
     * Get the customer that owns the Alamat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function namacustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }
}
