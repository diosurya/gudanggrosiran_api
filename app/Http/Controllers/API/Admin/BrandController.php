<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    /**
     * Display a listing of brands
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Base query dengan LEFT JOIN untuk count products
            $query = DB::table('brands as b')
                ->leftJoin('products as p', 'b.id', '=', 'p.brand_id')
                ->select(
                    'b.*',
                    DB::raw('COUNT(p.id) as products_count')
                );

            // Search functionality
            if ($request->has('search') && !empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('b.name', 'like', "%{$search}%")
                      ->orWhere('b.description', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && !empty($request->get('status'))) {
                $query->where('b.status', $request->get('status'));
            }

            // Group by untuk menghindari duplikasi karena LEFT JOIN
            // Sesuaikan dengan kolom yang benar-benar ada di tabel brands
            $query->groupBy('b.id');

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            // Validasi sort column untuk keamanan
            $allowedSortColumns = ['name', 'created_at', 'updated_at', 'products_count'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'name';
            }

            // Handle sorting untuk products_count (karena itu aggregate function)
            if ($sortBy === 'products_count') {
                $query->orderBy(DB::raw('COUNT(p.id)'), $sortOrder);
            } else {
                $query->orderBy("b.{$sortBy}", $sortOrder);
            }

            // Pagination or all
            if ($request->get('all') === 'true') {
                $brands = $query->get();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Brands retrieved successfully',
                    'data' => $brands
                ]);
            } else {
                // Manual pagination dengan DB builder
                $perPage = (int) $request->get('per_page', 15);
                $page = (int) $request->get('page', 1);
                $offset = ($page - 1) * $perPage;

                // Clone query untuk count total
                $countQuery = clone $query;
                $total = $countQuery->count(DB::raw('DISTINCT b.id'));

                // Apply pagination
                $brands = $query->limit($perPage)->offset($offset)->get();

                // Calculate pagination info
                $lastPage = ceil($total / $perPage);
                $from = $offset + 1;
                $to = min($offset + $perPage, $total);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Brands retrieved successfully',
                    'data' => [
                        'data' => $brands,
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $total,
                        'last_page' => $lastPage,
                        'from' => $from > $total ? null : $from,
                        'to' => $total > 0 ? $to : null,
                        'has_more_pages' => $page < $lastPage
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'status' => 'sometimes|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $brandData = $request->only([
                'name', 'description', 'logo_url', 'website_url', 'status', 
                'sort_order', 'meta_title', 'meta_description', 'meta_keywords'
            ]);

            // Generate slug
            $brandData['slug'] = Str::slug($request->name);
            
            // Ensure unique slug
            $originalSlug = $brandData['slug'];
            $count = 1;
            while (Brand::where('slug', $brandData['slug'])->exists()) {
                $brandData['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            // Set default status if not provided
            if (!isset($brandData['status'])) {
                $brandData['status'] = 'active';
            }

            $brand = Brand::create($brandData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Brand created successfully',
                'data' => $brand
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified brand
     */
    public function show($id): JsonResponse
    {
        try {
            $brand = Brand::with(['products'])->withCount('products')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Brand retrieved successfully',
                'data' => $brand
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'status' => 'sometimes|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $brandData = $request->only([
                'name', 'description', 'logo_url', 'website_url', 'status', 
                'sort_order', 'meta_title', 'meta_description', 'meta_keywords'
            ]);

            // Update slug if name changed
            if ($request->has('name') && $request->name !== $brand->name) {
                $brandData['slug'] = Str::slug($request->name);
                
                // Ensure unique slug
                $originalSlug = $brandData['slug'];
                $count = 1;
                while (Brand::where('slug', $brandData['slug'])->where('id', '!=', $brand->id)->exists()) {
                    $brandData['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $brand->update($brandData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Brand updated successfully',
                'data' => $brand
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified brand
     */
    public function destroy($id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Check if brand has products
            if ($brand->products()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete brand that has products'
                ], 422);
            }

            $brand->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Brand deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular brands
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            
            $brands = Brand::where('status', 'active')
                ->withCount('products')
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Popular brands retrieved successfully',
                'data' => $brands
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve popular brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update brand status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Brand::whereIn('id', $request->ids)
                 ->update(['status' => $request->status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Brands status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update brands status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete brands
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $brands = Brand::whereIn('id', $request->ids)->get();

            foreach ($brands as $brand) {
                if ($brand->products()->count() > 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Cannot delete brand '{$brand->name}' that has products"
                    ], 422);
                }
            }

            Brand::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Brands deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}