<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
                ->leftJoin('users as u', 'b.author_id', '=', 'u.id')
                ->select([
                    'b.*',
                    'bc.name as category_name',
                    'u.id as author_id',
                    'u.name as author_name',
                    'u.email as author_email',
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

            $blogIds = $blogs->pluck('id');

            // Cover images
            $coverImages = DB::table('blog_images')
                ->whereIn('blog_id', $blogIds)
                ->where('is_cover', true)
                ->pluck('path', 'blog_id');

            // Tags
            $blogTags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->whereIn('bt.blog_id', $blogIds)
                ->select('bt.blog_id', 't.name', 't.slug', 't.color')
                ->get()
                ->groupBy('blog_id');

            // Author photos
            $authorIds = $blogs->pluck('author_id')->filter();
            $authorPhotos = DB::table('user_photos')
                ->whereIn('user_id', $authorIds)
                ->pluck('user_id');

            // Map result
            $blogs = $blogs->map(function ($blog) use ($coverImages, $blogTags, $authorPhotos) {
                $blogArray = (array) $blog;
                $blogArray['cover_image'] = $coverImages[$blog->id] ?? null;
                $blogArray['tags'] = $blogTags[$blog->id] ?? [];
                $blogArray['author_photo'] = $authorPhotos[$blog->author_id] ?? null;
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
            // Log incoming request for debugging
            Log::info('Blog store request', [
                'files' => $request->hasFile('images') ? 'Yes' : 'No',
                'file_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
                'status' => $request->input('status')
            ]);

            // Log file details if present
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    Log::info("File {$index}", [
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension()
                    ]);
                }
            }

            $validated = $request->validate([
                'title'           => 'required|string|max:255',
                'slug'            => 'required|string|max:255|unique:blogs,slug',
                'excerpt'         => 'nullable|string',
                'content'         => 'nullable|string',
                'seo_title'       => 'nullable|string|max:255',
                'seo_description' => 'nullable|string',
                'seo_keywords'    => 'nullable|string',
                'meta_title'      => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords'   => 'nullable|string',
                'category_id'     => 'nullable|uuid|exists:blog_categories,id',
                'author_id'       => 'nullable|uuid|exists:users,id',
                'status'          => 'required|in:draft,published,deactivated',
                'images.*'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);


            $blog = Blog::create([
                'title'           => $validated['title'],
                'slug'            => $validated['slug'],
                'excerpt'         => $validated['excerpt'] ?? null,
                'content'         => $validated['content'] ?? null,
                'seo_title'       => $validated['seo_title'] ?? null,
                'seo_description' => $validated['seo_description'] ?? null,
                'seo_keywords'    => $validated['seo_keywords'] ?? null,
                'meta_title'      => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords'   => $validated['meta_keywords'] ?? null,
                'category_id'     => $validated['category_id'] ?? null,
                'author_id'     => $validated['author_id'] ?? null,
                'status'          => $validated['status'],
            ]);

            // Save images (if any)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    try {
                        // Store the image
                        $path = $image->store('blogs', 'public'); // storage/app/public/blogs
                        $isCover = $index === 0;

                        // Insert into blog_images table
                        DB::table('blog_images')->insert([
                            'id'         => (string) Str::uuid(),
                            'blog_id'    => $blog->id,
                            'path'       => '/storage/' . $path,
                            'is_cover'   => $isCover,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        Log::info("Image {$index} saved successfully", [
                            'path' => $path,
                            'is_cover' => $isCover
                        ]);

                    } catch (Exception $imageError) {
                        Log::error("Failed to save image {$index}", [
                            'error' => $imageError->getMessage(),
                            'file_name' => $image->getClientOriginalName()
                        ]);
                        // Continue with other images instead of failing completely
                    }
                }
            }

            // Fetch the newly created blog with category information
            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as c', 'b.category_id', '=', 'c.id')
                ->select([
                    'b.*',
                    'c.name as category_name'
                ])
                ->where('b.id', $blog->id)
                ->first();

            DB::commit();

            Log::info('Blog created successfully', ['blog_id' => $blog->id]);

            return response()->json([
                'success' => true,
                'message' => 'Blog created successfully',
                'data' => $blog
            ], 201);

        } catch (ValidationException $e) {
            DB::rollback();
            Log::error('Blog validation failed', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to create blog', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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

            // Get blog images
            $images = DB::table('blog_images')
                ->where('blog_id', $id)
                ->get();

            // Get blog tags
            $tags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->where('bt.blog_id', $id)
                ->select('t.id', 't.name', 't.slug', 't.color')
                ->get();

            // Add additional data to blog object
            $blogArray = (array) $blog;
            $blogArray['images'] = $images;
            $blogArray['tags'] = $tags;
            $blogArray['image_url'] = $images->where('is_cover', true)->first()->path ?? null;

            return response()->json([
                'success' => true,
                'data' => $blogArray
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
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

            $validated = $request->validate([
                'title'           => 'required|string|max:255',
                'slug'            => 'required|string|max:255|unique:blogs,slug,' . $id,
                'excerpt'         => 'nullable|string',
                'content'         => 'nullable|string',
                'meta_title'      => 'nullable|string|max:255',
                'meta_description'=> 'nullable|string',
                'meta_keywords'   => 'nullable|string',
                'category_id'     => 'required|uuid|exists:blog_categories,id',
                'author_id'       => 'nullable|uuid|exists:users,id',
                'status'          => 'required|in:draft,published,deactived',
                'images.*'        => 'nullable|image|mimes:jpg,jpeg,png',
            ]);

            $updateData = [
                'title'           => $validated['title'],
                'slug'            => $validated['slug'],
                'excerpt'         => $validated['excerpt'] ?? null,
                'content'         => $validated['content'] ?? null,
                'meta_title'      => $validated['meta_title'] ?? null,
                'meta_description'=> $validated['meta_description'] ?? null,
                'meta_keywords'   => $validated['meta_keywords'] ?? null,
                'category_id'     => $validated['category_id'],
                'author_id'       => $validated['author_id'],
                'status'          => $validated['status'],
                'updated_at'      => now()
            ];

            DB::table('blogs')->where('id', $id)->update($updateData);

            // Handle new images if uploaded
            if ($request->hasFile('images')) {

                $oldImages = DB::table('blog_images')->where('blog_id', $id)->get();
                foreach ($oldImages as $oldImage) {
                    $imagePath = str_replace('/storage/', '', $oldImage->path);
                    Storage::disk('public')->delete($imagePath);
                }
                DB::table('blog_images')->where('blog_id', $id)->delete();


                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('blogs', 'public');
                    $isCover = $index === 0;

                    DB::table('blog_images')->insert([
                        'id'         => (string) Str::uuid(),
                        'blog_id'    => $id,
                        'path'       => '/storage/' . $path,
                        'is_cover'   => $isCover,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $updatedBlog = DB::table('blogs as b')
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
                'data' => $updatedBlog
            ]);

            Log::info('Files', $request->file('images') ?? []);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog: ' . $e->getMessage()
            ], 500);
        }
    }

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

            // Delete blog images from storage
            $images = DB::table('blog_images')->where('blog_id', $id)->get();
            foreach ($images as $image) {
                $imagePath = str_replace('/storage/', '', $image->path);
                Storage::disk('public')->delete($imagePath);
            }

            // Delete blog images records
            DB::table('blog_images')->where('blog_id', $id)->delete();

            // Delete blog tags relations
            DB::table('blog_tag')->where('blog_id', $id)->delete();

            // Delete blog
            DB::table('blogs')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

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

}
