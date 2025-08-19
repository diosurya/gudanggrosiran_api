<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlamatResource;
use App\Http\Resources\DetailAlamatResource;
use App\Models\Alamat;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    public function index()
    {
        $alamats = Alamat::all();
        return AlamatResource::collection($alamats);
    }

    public function detail($id)
    {
        $alamat = Alamat::with('namacustomer')->findOrFail($id);
        return new DetailAlamatResource($alamat);
    }
}
