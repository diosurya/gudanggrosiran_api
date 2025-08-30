<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        return response()->json(Page::with('bannerImage')->paginate(10));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:pages,slug',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'banner_image_id' => 'nullable|exists:media,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

        $page = Page::create([
            'id' => (string) Str::uuid(),
            'slug' => $request->slug,
            'title' => $request->title,
            'content' => $request->content,
            'banner_image_id' => $request->banner_image_id,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        return response()->json(['success' => true, 'data' => $page], 201);
    }

    public function show(Page $page)
    {
        return response()->json($page->load('bannerImage'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'slug' => 'required|unique:pages,slug,' . $page->id,
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'banner_image_id' => 'nullable|exists:media,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

        $page->update($request->all());

        return response()->json(['success' => true, 'data' => $page]);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(['success' => true]);
    }
}
