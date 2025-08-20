<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $posts = Blog::with(['author', 'category', 'stats'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|unique:posts,slug',
            'content'     => 'required',
            'status'      => 'required|string|max:255',
            'excerpt'     => 'nullable|string',
            'seo_title'   => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
            'image_url'   => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'author_id'   => 'required|exists:authors,id',
            'published_at'=> 'nullable|date',
        ]);

        $post = Blog::create($validated);
        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Blog::with(['author', 'category', 'comments', 'stats'])->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Blog::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'slug'        => 'sometimes|required|string|unique:posts,slug,' . $post->id,
            'content'     => 'sometimes|required',
            'status'      => 'required|string|max:255',
            'excerpt'     => 'nullable|string',
            'seo_title'   => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
            'image_url'   => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'author_id'   => 'sometimes|required|exists:authors,id',
            'published_at'=> 'nullable|date',
        ]);

        $post->update($validated);
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Blog::findOrFail($id);
        $post->delete();
        return response()->json(null, 204);
    }
}
