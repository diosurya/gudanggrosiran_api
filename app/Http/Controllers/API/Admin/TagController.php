<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Menggunakan DB Builder instead of Eloquent
            $query = DB::table('tags as t')
                ->leftJoin('product_tags as pt', 't.id', '=', 'pt.tag_id')
                ->select(
                    't.*',
                    DB::raw('COUNT(pt.product_id) as products_count')
                );

            // Search functionality
            if ($request->has('search') && !empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('t.name', 'like', "%{$search}%")
                      ->orWhere('t.description', 'like', "%{$search}%");
                });
            }

            // Filter by status (uncomment jika kolom status ada)
            if ($request->has('status') && !empty($request->get('status'))) {
                $query->where('t.status', $request->get('status'));
            }

            // Filter by color
            if ($request->has('color') && !empty($request->get('color'))) {
                $query->where('t.color', $request->get('color'));
            }

            // Group by untuk menghindari duplikasi karena LEFT JOIN
            $query->groupBy('t.id');

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            // Validasi sort column untuk keamanan
            $allowedSortColumns = ['name', 'color', 'status', 'created_at', 'updated_at', 'products_count'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'name';
            }

            // Handle sorting untuk products_count (karena itu aggregate function)
            if ($sortBy === 'products_count') {
                $query->orderBy(DB::raw('COUNT(pt.product_id)'), $sortOrder);
            } else {
                $query->orderBy("t.{$sortBy}", $sortOrder);
            }

            // Pagination or all
            if ($request->get('all') === 'true') {
                $tags = $query->get();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tags retrieved successfully',
                    'data' => $tags
                ]);
            } else {
                // Manual pagination dengan DB builder
                $perPage = (int) $request->get('per_page', 15);
                $page = (int) $request->get('page', 1);
                $offset = ($page - 1) * $perPage;

                // Clone query untuk count total
                $countQuery = clone $query;
                $total = $countQuery->count(DB::raw('DISTINCT t.id'));

                // Apply pagination
                $tags = $query->limit($perPage)->offset($offset)->get();

                // Calculate pagination info
                $lastPage = ceil($total / $perPage);
                $from = $offset + 1;
                $to = min($offset + $perPage, $total);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Tags retrieved successfully',
                    'data' => [
                        'data' => $tags,
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
                'message' => 'Failed to retrieve tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string',
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
            $tagData = $request->only([
                'name', 'description', 'color', 'status', 'sort_order',
                'meta_title', 'meta_description', 'meta_keywords'
            ]);

            // Generate slug
            $tagData['slug'] = Str::slug($request->name);
            
            // Ensure unique slug menggunakan DB builder
            $originalSlug = $tagData['slug'];
            $count = 1;
            while (DB::table('tags')->where('slug', $tagData['slug'])->exists()) {
                $tagData['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            // Set default values
            if (!isset($tagData['status'])) {
                $tagData['status'] = 'active';
            }
            
            if (!isset($tagData['color']) || empty($tagData['color'])) {
                $tagData['color'] = '#1976d2';
            }

            // Add timestamps
            $tagData['created_at'] = now();
            $tagData['updated_at'] = now();

            // Insert menggunakan DB builder
            $tagId = DB::table('tags')->insertGetId($tagData);
            
            // Get the created tag
            $tag = DB::table('tags')->where('id', $tagId)->first();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tag created successfully',
                'data' => $tag
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tag
     */
    public function show($id): JsonResponse
    {
        try {
            // Get tag dengan products count menggunakan DB builder
            $tag = DB::table('tags as t')
                ->leftJoin('product_tags as pt', 't.id', '=', 'pt.tag_id')
                ->leftJoin('products as p', 'pt.product_id', '=', 'p.id')
                ->where('t.id', $id)
                ->select(
                    't.*',
                    DB::raw('COUNT(pt.product_id) as products_count')
                )
                ->groupBy('t.id')
                ->first();

            if (!$tag) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tag not found'
                ], 404);
            }

            // Get related products (optional)
            $products = DB::table('products as p')
                ->join('product_tags as pt', 'p.id', '=', 'pt.product_id')
                ->where('pt.tag_id', $id)
                ->select('p.*')
                ->get();

            $tag->products = $products;

            return response()->json([
                'status' => 'success',
                'message' => 'Tag retrieved successfully',
                'data' => $tag
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Check if tag exists
            $tag = DB::table('tags')->where('id', $id)->first();
            if (!$tag) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tag not found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $id,
            'description' => 'nullable|string',
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
            $tagData = $request->only([
                'name', 'description', 'color', 'status', 'sort_order',
                'meta_title', 'meta_description', 'meta_keywords'
            ]);

            // Update slug if name changed
            if ($request->has('name') && $request->name !== $tag->name) {
                $tagData['slug'] = Str::slug($request->name);
                
                // Ensure unique slug
                $originalSlug = $tagData['slug'];
                $count = 1;
                while (DB::table('tags')->where('slug', $tagData['slug'])->where('id', '!=', $id)->exists()) {
                    $tagData['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Add updated timestamp
            $tagData['updated_at'] = now();

            // Update menggunakan DB builder
            DB::table('tags')->where('id', $id)->update($tagData);

            // Get updated tag
            $updatedTag = DB::table('tags')->where('id', $id)->first();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tag updated successfully',
                'data' => $updatedTag
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tag
     */
    public function destroy($id): JsonResponse
    {
        try {
            // Check if tag exists
            $tag = DB::table('tags')->where('id', $id)->first();
            if (!$tag) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tag not found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Detach from all products before deleting
            DB::table('product_tags')->where('tag_id', $id)->delete();
            
            // Delete tag
            DB::table('tags')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tag deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular tags
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->get('limit', 10);
            
            $tags = DB::table('tags as t')
                ->leftJoin('product_tags as pt', 't.id', '=', 'pt.tag_id')
                ->select(
                    't.*',
                    DB::raw('COUNT(pt.product_id) as products_count')
                )
                ->where('t.status', 'active')
                ->groupBy('t.id')
                ->having('products_count', '>', 0)
                ->orderBy(DB::raw('COUNT(pt.product_id)'), 'desc')
                ->orderBy('t.name')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Popular tags retrieved successfully',
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve popular tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tags by color
     */
    public function byColor(Request $request, $color): JsonResponse
    {
        try {
            // Validate color format
            // if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Invalid color format'
            //     ], 400);
            // }

            $tags = DB::table('tags as t')
                ->leftJoin('product_tags as pt', 't.id', '=', 'pt.tag_id')
                ->select(
                    't.*',
                    DB::raw('COUNT(pt.product_id) as products_count')
                )
                ->where('t.color', $color)
                ->where('t.status', 'active')
                ->groupBy('t.id')
                ->orderBy('t.name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Tags by color retrieved successfully',
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tags by color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique colors used by tags
     */
    public function colors(): JsonResponse
    {
        try {
            $colors = DB::table('tags')
                ->where('status', 'active')
                ->select('color')
                ->distinct()
                ->orderBy('color')
                ->pluck('color');

            return response()->json([
                'status' => 'success',
                'message' => 'Tag colors retrieved successfully',
                'data' => $colors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tag colors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update tag status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:tags,id',
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
            $affected = DB::table('tags')
                ->whereIn('id', $request->ids)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tags status updated successfully',
                'affected_rows' => $affected
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update tags status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete tags
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:tags,id'
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
            // Delete pivot records first
            DB::table('product_tags')->whereIn('tag_id', $request->ids)->delete();
            
            // Delete tags
            $deleted = DB::table('tags')->whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tags deleted successfully',
                'deleted_count' => $deleted
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tags for autocomplete
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $limit = (int) $request->get('limit', 10);

            if (empty($query)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No search query provided',
                    'data' => []
                ]);
            }

            $tags = DB::table('tags')
                ->where('status', 'active')
                ->where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit($limit)
                ->select('id', 'name', 'color')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Tags search completed',
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup unused tags
     */
    public function cleanup(): JsonResponse
    {
        try {
            // Get tags yang tidak memiliki relasi dengan products
            $unusedTagIds = DB::table('tags as t')
                ->leftJoin('product_tags as pt', 't.id', '=', 'pt.tag_id')
                ->whereNull('pt.tag_id')
                ->pluck('t.id');

            $deletedCount = 0;
            if ($unusedTagIds->isNotEmpty()) {
                $deletedCount = DB::table('tags')->whereIn('id', $unusedTagIds)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} unused tags",
                'deleted_count' => $deletedCount
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup tags: ' . $e->getMessage()
            ], 500);
        }
    }
}