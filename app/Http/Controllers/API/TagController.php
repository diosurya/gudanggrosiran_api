<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;

class TagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'usage_count');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = DB::table('tags')->select([
                'id',
                'name',
                'slug',
                'description',
                'color',
                'usage_count',
                'created_at',
                'updated_at'
            ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sorting
            $allowedSortFields = ['name', 'usage_count', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $total = $query->count();
            $tags = $query
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tags,
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
                'message' => 'Failed to fetch tags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:tags,name',
                'description' => 'nullable|string',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/'
            ]);

            $data['id'] = Str::uuid();
            $data['slug'] = Str::slug($data['name']);
            
            // Check slug uniqueness
            $count = 1;
            $originalSlug = $data['slug'];
            while (DB::table('tags')->where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            $data['usage_count'] = 0;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('tags')->insert($data);

            $tag = DB::table('tags')->where('id', $data['id'])->first();

            return response()->json([
                'success' => true,
                'message' => 'Tag created successfully',
                'data' => $tag
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tag
     */
    public function show(string $id): JsonResponse
    {
        try {
            $tag = DB::table('tags')->where('id', $id)->first();

            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag not found'
                ], 404);
            }

            // Get products using this tag
            $products = DB::table('product_tag as pt')
                ->join('products as p', 'pt.product_id', '=', 'p.id')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.price',
                    'p.discount_price',
                    'p.average_rating',
                    'pc.name as category_name'
                ])
                ->where('pt.tag_id', $id)
                ->where('p.status', 'published')
                ->orderBy('p.created_at', 'desc')
                ->limit(10)
                ->get();

            // Get blogs using this tag
            $blogs = DB::table('blog_tag as bt')
                ->join('blogs as b', 'bt.blog_id', '=', 'b.id')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.id',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.reading_time',
                    'b.published_at',
                    'bc.name as category_name'
                ])
                ->where('bt.tag_id', $id)
                ->where('b.status', 'published')
                ->orderBy('b.published_at', 'desc')
                ->limit(10)
                ->get();

            $tagData = (array) $tag;
            $tagData['products'] = $products;
            $tagData['blogs'] = $blogs;

            return response()->json([
                'success' => true,
                'data' => $tagData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $exists = DB::table('tags')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag not found'
                ], 404);
            }

            $data = $request->validate([
                'name' => 'required|string|max:255|unique:tags,name,' . $id,
                'description' => 'nullable|string',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/'
            ]);

            $currentTag = DB::table('tags')->where('id', $id)->first();
            
            if ($data['name'] !== $currentTag->name) {
                $data['slug'] = Str::slug($data['name']);
                
                // Check slug uniqueness (exclude current record)
                $count = 1;
                $originalSlug = $data['slug'];
                while (DB::table('tags')
                    ->where('slug', $data['slug'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $data['updated_at'] = now();

            DB::table('tags')->where('id', $id)->update($data);

            $tag = DB::table('tags')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully',
                'data' => $tag
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tag
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $tag = DB::table('tags')->where('id', $id)->first();
            
            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag not found'
                ], 404);
            }

            // Remove tag associations
            DB::table('product_tag')->where('tag_id', $id)->delete();
            DB::table('blog_tag')->where('tag_id', $id)->delete();

            // Delete the tag
            DB::table('tags')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular tags
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 20);

            $tags = DB::table('tags')
                ->select([
                    'id',
                    'name',
                    'slug',
                    'color',
                    'usage_count'
                ])
                ->where('usage_count', '>', 0)
                ->orderBy('usage_count', 'desc')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tags
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular tags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tags
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            $limit = $request->get('limit', 10);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }

            $tags = DB::table('tags')
                ->select([
                    'id',
                    'name',
                    'slug',
                    'color',
                    'usage_count'
                ])
                ->where('name', 'like', "%{$query}%")
                ->orderBy('usage_count', 'desc')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tags,
                'query' => $query
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search tags: ' . $e->getMessage()
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