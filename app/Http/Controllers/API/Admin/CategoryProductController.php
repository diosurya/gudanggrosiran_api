<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class CategoryProductController extends Controller
{
     public function index(): JsonResponse
    {
        $category_products = CategoryProduct::with(['parent', 'children'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($category_products);
    }

    public function show(CategoryProduct $category): JsonResponse
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

        $category = CategoryProduct::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, CategoryProduct $category): JsonResponse
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

    public function destroy(CategoryProduct $category): JsonResponse
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
