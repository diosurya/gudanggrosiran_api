<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailAlamatResource extends JsonResource
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
            "nama_penerima" => $this->nama_penerima,
            "nama_alamat" => $this->nama_alamat,
            "detail_alamat" => $this->detail_alamat,
            "no_hp" => $this->no_hp,
            "kode_pos" => $this->kode_pos,
            "nama_customer" => $this->namacustomer,
        ];
    }
}
