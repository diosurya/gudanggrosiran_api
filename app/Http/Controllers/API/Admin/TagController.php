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
            $query = Tag::query();

            // Include products count
            $query->withCount('products');

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by color
            if ($request->has('color')) {
                $query->where('color', $request->get('color'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination or all
            if ($request->get('all') === 'true') {
                $tags = $query->get();
            } else {
                $perPage = $request->get('per_page', 15);
                $tags = $query->paginate($perPage);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Tags retrieved successfully',
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tags',
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
            $tag = Tag::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
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
                while (Tag::where('slug', $tagData['slug'])->where('id', '!=', $tag->id)->exists()) {
                    $tagData['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $tag->update($tagData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tag updated successfully',
                'data' => $tag
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
            $tag = Tag::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Detach from all products before deleting
            $tag->products()->detach();
            $tag->delete();

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
            $limit = $request->get('limit', 10);
            
            $tags = Tag::where('status', 'active')
                ->withCount('products')
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->orderBy('name')
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
            if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid color format'
                ], 400);
            }

            $tags = Tag::where('color', $color)
                ->where('status', 'active')
                ->withCount('products')
                ->orderBy('name')
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
            $colors = Tag::where('status', 'active')
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
            Tag::whereIn('id', $request->ids)
               ->update(['status' => $request->status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tags status updated successfully'
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
            $tags = Tag::whereIn('id', $request->ids)->get();

            foreach ($tags as $tag) {
                $tag->products()->detach();
                $tag->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tags deleted successfully'
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
            $limit = $request->get('limit', 10);

            if (empty($query)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No search query provided',
                    'data' => []
                ]);
            }

            $tags = Tag::where('status', 'active')
                ->where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit($limit)
                ->get(['id', 'name', 'color']);

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
     * Store a newly created tag
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
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
            
            // Ensure unique slug
            $originalSlug = $tagData['slug'];
            $count = 1;
            while (Tag::where('slug', $tagData['slug'])->exists()) {
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

            $tag = Tag::create($tagData);

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
            $tag = Tag::with(['products'])->withCount('products')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Tag retrieved successfully',
                'data' => $tag
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tag',
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
            $deletedCount = DB::table('tags')->where('usage_count', 0)->delete();

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