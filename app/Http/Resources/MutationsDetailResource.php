<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MutationsDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'jumlah_saldo_tf' => $this->jumlah_saldo_tf,
            'id_pengirim' => $this->id_pengirim,
            'saldo_awal_pengirim' => $this->saldo_awal_pengirim,
            'saldo_akhir_pengirim' => $this->saldo_akhir_pengirim,
            'id_penerima' => $this->id_penerima,
            'saldo_awal_penerima' => $this->saldo_awal_penerima,
            'saldo_akhir_penerima' => $this->saldo_akhir_penerima,
            'created_at' => date_format($this->created_at, 'Y/m/d'),
        ];
    }
}
