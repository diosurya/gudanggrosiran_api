<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
     public function index(): JsonResponse
    {
        $category_products = CategoryProducts::with(['parent', 'children'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($category_products);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['parent', 'children', 'products']);
        return response()->json($category);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:category_products,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:category_products,id',
            'is_active' => 'boolean',
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:category_products,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:category_products,id',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
