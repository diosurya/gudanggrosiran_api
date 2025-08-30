<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $parentId = $request->get('parent_id');
            $isActive = $request->get('is_active');

            $query = DB::table('product_categories as pc')
                ->leftJoin('product_categories as parent', 'pc.parent_id', '=', 'parent.id')
                ->select([
                    'pc.id',
                    'pc.parent_id',
                    'pc.name',
                    'pc.slug',
                    'pc.description',
                    'pc.image',
                    'pc.sort_order',
                    'pc.is_active',
                    'pc.meta_title',
                    'pc.meta_description',
                    'pc.created_at',
                    'pc.updated_at',
                    'parent.name as parent_name'
                ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pc.name', 'like', "%{$search}%")
                      ->orWhere('pc.description', 'like', "%{$search}%");
                });
            }

            if (!is_null($parentId)) {
                if ($parentId === 'null') {
                    $query->whereNull('pc.parent_id');
                } else {
                    $query->where('pc.parent_id', $parentId);
                }
            }

            if (!is_null($isActive)) {
                $query->where('pc.is_active', $isActive);
            }

            $total = $query->count();
            $categories = $query
                ->orderBy('pc.sort_order')
                ->orderBy('pc.name')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product category
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'parent_id' => 'nullable|uuid|exists:product_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'sort_order' => 'integer|min:0',
                'is_active' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string'
            ]);

            $data['id'] = Str::uuid();
            $data['slug'] = Str::slug($data['name']);
            
            // Check slug uniqueness
            $count = 1;
            $originalSlug = $data['slug'];
            while (DB::table('product_categories')->where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('product_categories')->insert($data);

            $category = DB::table('product_categories as pc')
                ->leftJoin('product_categories as parent', 'pc.parent_id', '=', 'parent.id')
                ->select([
                    'pc.*',
                    'parent.name as parent_name'
                ])
                ->where('pc.id', $data['id'])
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product category
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = DB::table('product_categories as pc')
                ->leftJoin('product_categories as parent', 'pc.parent_id', '=', 'parent.id')
                ->select([
                    'pc.*',
                    'parent.name as parent_name'
                ])
                ->where('pc.id', $id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            // Get children categories
            $children = DB::table('product_categories')
                ->where('parent_id', $id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'image', 'sort_order']);

            // Get products count
            $productsCount = DB::table('products')
                ->where('category_id', $id)
                ->where('status', 'published')
                ->count();

            $categoryData = (array) $category;
            $categoryData['children'] = $children;
            $categoryData['products_count'] = $productsCount;

            return response()->json([
                'success' => true,
                'data' => $categoryData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product category
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $exists = DB::table('product_categories')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $data = $request->validate([
                'parent_id' => 'nullable|uuid|exists:product_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'sort_order' => 'integer|min:0',
                'is_active' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string'
            ]);

            // Check if parent_id creates circular reference
            if (!empty($data['parent_id'])) {
                $parentChain = [];
                $currentParent = $data['parent_id'];
                
                while ($currentParent) {
                    if ($currentParent === $id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Circular reference detected'
                        ], 422);
                    }
                    $parentChain[] = $currentParent;
                    $parent = DB::table('product_categories')
                        ->where('id', $currentParent)
                        ->value('parent_id');
                    $currentParent = $parent;
                }
            }

            $currentCategory = DB::table('product_categories')->where('id', $id)->first();
            
            if ($data['name'] !== $currentCategory->name) {
                $data['slug'] = Str::slug($data['name']);
                
                // Check slug uniqueness (exclude current record)
                $count = 1;
                $originalSlug = $data['slug'];
                while (DB::table('product_categories')
                    ->where('slug', $data['slug'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $data['updated_at'] = now();

            DB::table('product_categories')->where('id', $id)->update($data);

            $category = DB::table('product_categories as pc')
                ->leftJoin('product_categories as parent', 'pc.parent_id', '=', 'parent.id')
                ->select([
                    'pc.*',
                    'parent.name as parent_name'
                ])
                ->where('pc.id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product category
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = DB::table('product_categories')->where('id', $id)->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            // Check if category has children
            $hasChildren = DB::table('product_categories')->where('parent_id', $id)->exists();
            if ($hasChildren) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has child categories'
                ], 422);
            }

            // Check if category has products
            $hasProducts = DB::table('products')->where('category_id', $id)->exists();
            if ($hasProducts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has products'
                ], 422);
            }

            DB::table('product_categories')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category tree structure
     */
    public function tree(): JsonResponse
    {
        try {
            $categories = DB::table('product_categories')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'name', 'slug', 'image', 'sort_order']);

            $tree = $this->buildTree($categories->toArray());

            return response()->json([
                'success' => true,
                'data' => $tree
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category tree: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build hierarchical tree from flat array
     */
    private function buildTree(array $categories, $parentId = null): array
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $children = $this->buildTree($categories, $category->id);
                $categoryArray = (array) $category;
                
                if (!empty($children)) {
                    $categoryArray['children'] = $children;
                }
                
                $tree[] = $categoryArray;
            }
        }
        
        return $tree;
    }
}