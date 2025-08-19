<?php

namespace App\Http\Controllers;

use App\Http\Resources\MutationsResource;
use App\Models\Mutation;
use Illuminate\Http\Request;

class MutationController extends Controller
{
    public function index()
    {
        $mutations = Mutation::all();
        $options['key'] = 'mutations';
        return MutationsResource::collection($mutations);
    }
}
