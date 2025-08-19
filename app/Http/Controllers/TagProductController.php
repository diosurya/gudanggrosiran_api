<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TagProductController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|unique:tags,slug',
            'color' => 'nullable|string|max:7',
        ]);

        $tag = Tag::create($validated);
        return response()->json($tag, 201);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:100',
            'slug' => 'string|unique:tags,slug,' . $tag->id,
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update($validated);
        return response()->json($tag);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
