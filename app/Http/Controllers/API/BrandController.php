<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;

class BrandController extends Controller
{
    /**
     * Display a listing of brands
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $isActive = $request->get('is_active');

            $query = DB::table('brands')->select([
                'id',
                'name',
                'slug',
                'description',
                'logo',
                'website',
                'is_active',
                'meta_title',
                'meta_description',
                'created_at',
                'updated_at'
            ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if (!is_null($isActive)) {
                $query->where('is_active', $isActive);
            }

            $total = $query->count();
            $brands = $query
                ->orderBy('name')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Add products count for each brand
            $brandIds = $brands->pluck('id');
            $productCounts = DB::table('products')
                ->select('brand_id', DB::raw('count(*) as products_count'))
                ->whereIn('brand_id', $brandIds)
                ->where('status', 'published')
                ->groupBy('brand_id')
                ->pluck('products_count', 'brand_id');

            $brands = $brands->map(function ($brand) use ($productCounts) {
                $brandArray = (array) $brand;
                $brandArray['products_count'] = $productCounts[$brand->id] ?? 0;
                return $brandArray;
            });

            return response()->json([
                'success' => true,
                'data' => $brands,
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
                'message' => 'Failed to fetch brands: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'website' => 'nullable|url',
                'is_active' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string'
            ]);

            $data['id'] = Str::uuid();
            $data['slug'] = Str::slug($data['name']);
            
            // Check slug uniqueness
            $count = 1;
            $originalSlug = $data['slug'];
            while (DB::table('brands')->where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('brands')->insert($data);

            $brand = DB::table('brands')->where('id', $data['id'])->first();
            $brandArray = (array) $brand;
            $brandArray['products_count'] = 0;

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully',
                'data' => $brandArray
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified brand
     */
    public function show(string $id): JsonResponse
    {
        try {
            $brand = DB::table('brands')->where('id', $id)->first();

            if (!$brand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            // Get products count
            $productsCount = DB::table('products')
                ->where('brand_id', $id)
                ->where('status', 'published')
                ->count();

            // Get recent products
            $recentProducts = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.price',
                    'p.discount_price',
                    'p.average_rating',
                    'p.created_at',
                    'pc.name as category_name'
                ])
                ->where('p.brand_id', $id)
                ->where('p.status', 'published')
                ->orderBy('p.created_at', 'desc')
                ->limit(5)
                ->get();

            $brandData = (array) $brand;
            $brandData['products_count'] = $productsCount;
            $brandData['recent_products'] = $recentProducts;

            return response()->json([
                'success' => true,
                'data' => $brandData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $exists = DB::table('brands')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'website' => 'nullable|url',
                'is_active' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string'
            ]);

            $currentBrand = DB::table('brands')->where('id', $id)->first();
            
            if ($data['name'] !== $currentBrand->name) {
                $data['slug'] = Str::slug($data['name']);
                
                // Check slug uniqueness (exclude current record)
                $count = 1;
                $originalSlug = $data['slug'];
                while (DB::table('brands')
                    ->where('slug', $data['slug'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $data['updated_at'] = now();

            DB::table('brands')->where('id', $id)->update($data);

            $brand = DB::table('brands')->where('id', $id)->first();
            
            // Get products count
            $productsCount = DB::table('products')
                ->where('brand_id', $id)
                ->where('status', 'published')
                ->count();

            $brandData = (array) $brand;
            $brandData['products_count'] = $productsCount;

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully',
                'data' => $brandData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified brand
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $brand = DB::table('brands')->where('id', $id)->first();
            
            if (!$brand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            // Check if brand has products
            $hasProducts = DB::table('products')->where('brand_id', $id)->exists();
            if ($hasProducts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete brand that has products'
                ], 422);
            }

            DB::table('brands')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand: ' . $e->getMessage()
            ], 500);
        }
    }
}