<?php

namespace App\Http\Controllers\Api\Landing;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        return response()->json(Page::with('bannerImage')->get());
    }

    public function showBySlug($slug)
    {
        $page = Page::with('bannerImage')->where('slug', $slug)->firstOrFail();
        return response()->json($page);
    }

    // public function show(Page $page)
    // {
    //     return response()->json($page->load('bannerImage'));
    // }
}
