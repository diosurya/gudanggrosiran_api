<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $categoryId = $request->get('category_id');
            $status = $request->get('status');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.id',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.reading_time',
                    'b.status',
                    'b.view_count',
                    'b.share_count',
                    'b.average_rating',
                    'b.published_at',
                    'b.created_at',
                    'b.updated_at',
                    'bc.name as category_name'
                ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('MATCH(b.title, b.content, b.excerpt) AGAINST(? IN BOOLEAN MODE)', [$search])
                      ->orWhere('b.title', 'like', "%{$search}%")
                      ->orWhere('b.excerpt', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $query->where('b.category_id', $categoryId);
            }

            if ($status) {
                $query->where('b.status', $status);
            }

            // Sorting
            $allowedSortFields = ['created_at', 'published_at', 'title', 'view_count', 'average_rating'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy('b.' . $sortBy, $sortOrder);
            }

            $total = $query->count();
            $blogs = $query
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get cover images for blogs
            $blogIds = $blogs->pluck('id');
            $coverImages = DB::table('blog_images')
                ->whereIn('blog_id', $blogIds)
                ->where('is_cover', true)
                ->pluck('path', 'blog_id');

            // Get tags for each blog
            $blogTags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->whereIn('bt.blog_id', $blogIds)
                ->select('bt.blog_id', 't.name', 't.slug', 't.color')
                ->get()
                ->groupBy('blog_id');

            $blogs = $blogs->map(function ($blog) use ($coverImages, $blogTags) {
                $blogArray = (array) $blog;
                $blogArray['cover_image'] = $coverImages[$blog->id] ?? null;
                $blogArray['tags'] = $blogTags[$blog->id] ?? [];
                return $blogArray;
            });

            return response()->json([
                'success' => true,
                'data' => $blogs,
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
                'message' => 'Failed to fetch blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created blog
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validate([
                'category_id' => 'required|uuid|exists:blog_categories,id',
                'title' => 'required|string|max:255',
                'excerpt' => 'nullable|string',
                'content' => 'nullable|string',
                'reading_time' => 'integer|min:0',
                'status' => 'in:draft,published,archived,scheduled',
                'author_id' => 'nullable|uuid',
                'published_at' => 'nullable|date',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string',
                'structured_data' => 'nullable|json',
                'tags' => 'array',
                'tags.*' => 'string'
            ]);

            $data['id'] = Str::uuid();
            $data['slug'] = Str::slug($data['title']);
            
            // Check slug uniqueness
            $count = 1;
            $originalSlug = $data['slug'];
            while (DB::table('blogs')->where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            // Auto calculate reading time if not provided
            if (empty($data['reading_time']) && !empty($data['content'])) {
                $wordCount = str_word_count(strip_tags($data['content']));
                $data['reading_time'] = max(1, ceil($wordCount / 200)); // 200 words per minute
            }

            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('blogs')->insert($data);

            // Handle tags
            if (!empty($tags)) {
                $this->syncBlogTags($data['id'], $tags);
            }

            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.*',
                    'bc.name as category_name'
                ])
                ->where('b.id', $data['id'])
                ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog created successfully',
                'data' => $blog
            ], 201);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified blog
     */
    public function show(string $id): JsonResponse
    {
        try {
            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.*',
                    'bc.name as category_name'
                ])
                ->where('b.id', $id)
                ->first();

            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found'
                ], 404);
            }

            // Delete related data (cascading deletes handled by foreign keys)
            DB::table('blogs')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get published blogs
     */
    public function published(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $categoryId = $request->get('category_id');

            $query = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.id',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.reading_time',
                    'b.view_count',
                    'b.average_rating',
                    'b.published_at',
                    'bc.name as category_name'
                ])
                ->where('b.status', 'published')
                ->where('b.published_at', '<=', now());

            if ($categoryId) {
                $query->where('b.category_id', $categoryId);
            }

            $total = $query->count();
            $blogs = $query
                ->orderBy('b.published_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get cover images
            $blogIds = $blogs->pluck('id');
            $coverImages = DB::table('blog_images')
                ->whereIn('blog_id', $blogIds)
                ->where('is_cover', true)
                ->pluck('path', 'blog_id');

            $blogs = $blogs->map(function ($blog) use ($coverImages) {
                $blogArray = (array) $blog;
                $blogArray['cover_image'] = $coverImages[$blog->id] ?? null;
                return $blogArray;
            });

            return response()->json([
                'success' => true,
                'data' => $blogs,
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
                'message' => 'Failed to fetch published blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search blogs
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }

            $blogs = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.id',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.reading_time',
                    'b.view_count',
                    'b.average_rating',
                    'b.published_at',
                    'bc.name as category_name'
                ])
                ->where('b.status', 'published')
                ->where(function ($q) use ($query) {
                    $q->whereRaw('MATCH(b.title, b.content, b.excerpt) AGAINST(? IN BOOLEAN MODE)', [$query])
                      ->orWhere('b.title', 'like', "%{$query}%")
                      ->orWhere('b.excerpt', 'like', "%{$query}%");
                })
                ->orderByRaw('MATCH(b.title, b.content, b.excerpt) AGAINST(? IN BOOLEAN MODE) DESC', [$query])
                ->orderBy('b.published_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get cover images
            $blogIds = $blogs->pluck('id');
            $coverImages = DB::table('blog_images')
                ->whereIn('blog_id', $blogIds)
                ->where('is_cover', true)
                ->pluck('path', 'blog_id');

            $blogs = $blogs->map(function ($blog) use ($coverImages) {
                $blogArray = (array) $blog;
                $blogArray['cover_image'] = $coverImages[$blog->id] ?? null;
                return $blogArray;
            });

            return response()->json([
                'success' => true,
                'data' => $blogs,
                'query' => $query
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync blog tags
     */
    /**
     * Sinkronisasi tags untuk blog
     */
    private function syncBlogTags(string $blogId, array $tagNames): void
    {
        // Hapus semua relasi lama
        DB::table('blog_tag')->where('blog_id', $blogId)->delete();

        if (empty($tagNames)) {
            return;
        }

        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) {
                continue;
            }

            $slug = Str::slug($tagName);

            // Cari berdasarkan slug (lebih aman dari case/space berbeda)
            $tag = DB::table('tags')->where('slug', $slug)->first();

            if (!$tag) {
                $tagId = (string) Str::uuid();
                DB::table('tags')->insert([
                    'id'          => $tagId,
                    'name'        => $tagName,
                    'slug'        => $slug,
                    'usage_count' => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            } else {
                $tagId = $tag->id;
                // Recalculate usage_count secara dinamis (bukan terus +1 supaya akurat)
                $count = DB::table('blog_tag')->where('tag_id', $tagId)->count();
                DB::table('tags')->where('id', $tagId)->update([
                    'usage_count' => $count + 1,
                    'updated_at'  => now()
                ]);
            }

            $tagIds[] = $tagId;
        }

        // Insert relasi blog-tag
        $insertData = [];
        foreach (array_unique($tagIds) as $tagId) {
            $insertData[] = [
                'blog_id' => $blogId,
                'tag_id'  => $tagId
            ];
        }

        if (!empty($insertData)) {
            DB::table('blog_tag')->insert($insertData);
        }
    }


    /**
     * Update the specified blog
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $exists = DB::table('blogs')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found'
                ], 404);
            }

            $data = $request->validate([
                'category_id' => 'required|uuid|exists:blog_categories,id',
                'title' => 'required|string|max:255',
                'excerpt' => 'nullable|string',
                'content' => 'nullable|string',
                'reading_time' => 'integer|min:0',
                'status' => 'in:draft,published,archived,scheduled',
                'author_id' => 'nullable|uuid',
                'published_at' => 'nullable|date',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string',
                'structured_data' => 'nullable|json',
                'tags' => 'array',
                'tags.*' => 'string'
            ]);

            $currentBlog = DB::table('blogs')->where('id', $id)->first();
            
            if ($data['title'] !== $currentBlog->title) {
                $data['slug'] = Str::slug($data['title']);
                
                $count = 1;
                $originalSlug = $data['slug'];
                while (DB::table('blogs')
                    ->where('slug', $data['slug'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Auto calculate reading time if not provided
            if (empty($data['reading_time']) && !empty($data['content'])) {
                $wordCount = str_word_count(strip_tags($data['content']));
                $data['reading_time'] = max(1, ceil($wordCount / 200));
            }

            if ($data['status'] === 'published' && empty($currentBlog->published_at)) {
                $data['published_at'] = now();
            }

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $data['updated_at'] = now();

            DB::table('blogs')->where('id', $id)->update($data);

            // Handle tags
            if (!empty($tags)) {
                $this->syncBlogTags($id, $tags);
            }

            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.*',
                    'bc.name as category_name'
                ])
                ->where('b.id', $id)
                ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog updated successfully',
                'data' => $blog
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified blog
     */
   /**
     * Remove the specified blog
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $blog = DB::table('blogs')->where('id', $id)->first();

            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found'
                ], 404);
            }

            // Hapus relasi tags (jika ada pivot table blog_tag misalnya)
            DB::table('blog_tag')->where('blog_id', $id)->delete();

            // Hapus blog
            DB::table('blogs')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully'
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog: ' . $e->getMessage()
            ], 500);
        }
    }
}
