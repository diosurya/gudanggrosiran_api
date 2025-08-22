<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    /**
     * List all categories with optional pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $categories = BlogCategory::with('blogs')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($categories);
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_categories,slug',
            'description' => 'nullable|string',
        ]);

        $category = BlogCategory::create($validated);
        return response()->json($category, 201);
    }

    /**
     * Show single category
     */
    public function show($id)
    {
        $category = BlogCategory::with('blogs')->findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|unique:blog_categories,slug,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Delete a category
     */
    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }
}
