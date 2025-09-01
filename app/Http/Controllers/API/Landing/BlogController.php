<?php

namespace App\Http\Controllers\Api\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class BlogController extends Controller
{
    /**
     * List semua blog
     */
    public function index(): JsonResponse
    {
        try {
            $blogs = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.id',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.status',
                    'b.published_at',
                    'bc.name as category_name',
                ])
                ->where('b.status', 'published')
                ->orderByDesc('b.published_at')
                ->get();

            $blogIds = $blogs->pluck('id');

            // Ambil cover images
            $coverImages = DB::table('blog_images')
                ->whereIn('blog_id', $blogIds)
                ->where('is_cover', true)
                ->pluck('path', 'blog_id');

            // Ambil tags per blog
            $tags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->whereIn('bt.blog_id', $blogIds)
                ->select('bt.blog_id', 't.id', 't.name', 't.slug', 't.color')
                ->get()
                ->groupBy('blog_id');

            // Mapping ke response
            $blogs = $blogs->map(function ($blog) use ($coverImages, $tags) {
                $blogArray = (array) $blog;
                $blogArray['cover_image'] = $coverImages[$blog->id] ?? null;
                $blogArray['tags'] = $tags[$blog->id] ?? [];
                return $blogArray;
            });

            return response()->json([
                'success' => true,
                'data' => $blogs
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show detail blog by slug
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.*',
                    'bc.name as category_name'
                ])
                ->where('b.slug', $slug)
                ->first();

            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found'
                ], 404);
            }

            // Ambil images
            $images = DB::table('blog_images')
                ->where('blog_id', $blog->id)
                ->get();

            // Ambil tags
            $tags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->where('bt.blog_id', $blog->id)
                ->select('t.id', 't.name', 't.slug', 't.color')
                ->get();

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

    /**
     * Show published blog by slug
     */
    public function publishedShow(string $slug): JsonResponse
    {
        try {
            $blog = DB::table('blogs as b')
                ->leftJoin('blog_categories as bc', 'b.category_id', '=', 'bc.id')
                ->select([
                    'b.*',
                    'bc.name as category_name'
                ])
                ->where('b.slug', $slug)
                ->where('b.status', 'published')
                ->first();

            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found or not published'
                ], 404);
            }

            // Ambil images
            $images = DB::table('blog_images')
                ->where('blog_id', $blog->id)
                ->get();

            // Ambil tags
            $tags = DB::table('blog_tag as bt')
                ->join('tags as t', 'bt.tag_id', '=', 't.id')
                ->where('bt.blog_id', $blog->id)
                ->select('t.id', 't.name', 't.slug', 't.color')
                ->get();

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
                'message' => 'Failed to fetch published blog: ' . $e->getMessage()
            ], 500);
        }
    }
}
