<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('pages')
                ->leftJoin('media', 'pages.banner_image_id', '=', 'media.id')
                ->select([
                    'pages.*',
                    'media.path as banner_image_url',
                    'media.name as banner_image_name'
                ])
                ->whereNull('pages.deleted_at');
            
            // Add search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('pages.title', 'like', "%{$search}%")
                      ->orWhere('pages.content', 'like', "%{$search}%")
                      ->orWhere('pages.slug', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                if ($request->status === 'published') {
                    $query->where('pages.is_published', true);
                } elseif ($request->status === 'draft') {
                    $query->where('pages.is_published', false);
                }
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;

            // Get total count
            $totalCount = $query->count();
            
            // Get paginated data
            $pages = $query->orderBy('pages.created_at', 'desc')
                          ->limit($perPage)
                          ->offset($offset)
                          ->get();

            // Transform data to include banner_image object
            $transformedPages = $pages->map(function($page) {
                $pageData = (array) $page;
                
                // Create banner_image object if exists
                if ($page->banner_image_id && $page->banner_image_url) {
                    $pageData['banner_image'] = [
                        'id' => $page->banner_image_id,
                        'url' => $page->banner_image_url,
                        'name' => $page->banner_image_name
                    ];
                } else {
                    $pageData['banner_image'] = null;
                }
                
                // Remove individual banner image fields
                unset($pageData['banner_image_url'], $pageData['banner_image_name']);
                
                return $pageData;
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPages,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page' => (int) $perPage,
                    'total' => $totalCount,
                    'last_page' => ceil($totalCount / $perPage),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'slug' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'banner_image_id' => 'nullable|exists:media,id',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|url|max:500',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string|max:500',
                'og_type' => 'nullable|string|max:100',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string',
                'twitter_image' => 'nullable|string|max:500',
                'twitter_card' => 'nullable|string|max:100',
                'structured_data' => 'nullable|json',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            // Check if slug exists
            $existingSlug = DB::table('pages')
                ->where('slug', $request->slug)
                ->whereNull('deleted_at')
                ->exists();

            if ($existingSlug) {
                throw ValidationException::withMessages([
                    'slug' => ['The slug has already been taken.']
                ]);
            }

            $data = $request->only([
                'slug', 'title', 'content', 'banner_image_id', 'meta_title',
                'meta_description', 'meta_keywords', 'canonical_url',
                'og_title', 'og_description', 'og_image', 'og_type',
                'twitter_title', 'twitter_description', 'twitter_image',
                'twitter_card', 'structured_data', 'is_published', 'published_at'
            ]);

            $data['id'] = (string) Str::uuid();
            $data['is_published'] = $request->get('is_published', false);

            // Set published_at if publishing
            if ($data['is_published'] && !$data['published_at']) {
                $data['published_at'] = now();
            } elseif (!$data['is_published']) {
                $data['published_at'] = null;
            }

            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('pages')->insert($data);

            $page = null;
            if (!empty($data['banner_image_id'])) {
                $page = $this->getPageWithBannerImage($data['id']);
            } else {
                $page = DB::table('pages')
                    ->where('id', $data['id'])
                    ->whereNull('deleted_at')
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => $page
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $page = DB::table('pages')
                ->leftJoin('media', 'pages.banner_image_id', '=', 'media.id')
                ->select([
                    'pages.*',
                    'media.path as banner_image_url',
                    'media.name as banner_image_name'
                ])
                ->where('pages.id', $id)
                ->whereNull('pages.deleted_at')
                ->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // format banner_image biar rapi
            $pageData = (array) $page;
            $pageData['banner_image'] = $page->banner_image_id && $page->banner_image_url
                ? [
                    'id' => $page->banner_image_id,
                    'url' => $page->banner_image_url,
                    'name' => $page->banner_image_name
                ]
                : null;

            unset($pageData['banner_image_url'], $pageData['banner_image_name']);

            return response()->json([
                'success' => true,
                'data' => $pageData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'slug' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'banner_image_id' => 'nullable|exists:media,id',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|url|max:500',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string|max:500',
                'og_type' => 'nullable|string|max:100',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string',
                'twitter_image' => 'nullable|string|max:500',
                'twitter_card' => 'nullable|string|max:100',
                'structured_data' => 'nullable|json',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            // Check if page exists
            $existingPage = DB::table('pages')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existingPage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // Check if slug exists for other pages
            $existingSlug = DB::table('pages')
                ->where('slug', $request->slug)
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists();

            if ($existingSlug) {
                throw ValidationException::withMessages([
                    'slug' => ['The slug has already been taken.']
                ]);
            }

            $data = $request->only([
                'slug', 'title', 'content', 'banner_image_id', 'meta_title',
                'meta_description', 'meta_keywords', 'canonical_url',
                'og_title', 'og_description', 'og_image', 'og_type',
                'twitter_title', 'twitter_description', 'twitter_image',
                'twitter_card', 'structured_data', 'is_published', 'published_at'
            ]);

            $data['is_published'] = $request->get('is_published', false);

            // Handle published_at logic
            if ($data['is_published'] && !$existingPage->published_at && !$data['published_at']) {
                $data['published_at'] = now();
            } elseif (!$data['is_published']) {
                $data['published_at'] = null;
            }

            $data['updated_at'] = now();

            DB::table('pages')
                ->where('id', $id)
                ->update($data);

            $page = null;
            if (!empty($data['banner_image_id'])) {
                $page = $this->getPageWithBannerImage($id);
            } else {
                $page = DB::table('pages')
                    ->where('id', $id)
                    ->whereNull('deleted_at')
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => $page
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $page = DB::table('pages')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // Soft delete
            DB::table('pages')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete page: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get page with banner image
     */
    private function getPageWithBannerImage($id)
    {
        $page = DB::table('pages')
            ->leftJoin('media', 'pages.banner_image_id', '=', 'media.id')
            ->select([
                'pages.*',
                'media.path as banner_image_url',
                'media.name as banner_image_name'
            ])
            ->where('pages.id', $id)
            ->whereNull('pages.deleted_at')
            ->first();

        if (!$page) {
            return null;
        }

        $pageData = (array) $page;

        // Create banner_image object if exists
        if ($page->banner_image_id && $page->banner_image_url) {
            $pageData['banner_image'] = [
                'id' => $page->banner_image_id,
                'url' => $page->banner_image_url,
                'name' => $page->banner_image_name
            ];
        } else {
            $pageData['banner_image'] = null;
        }

        // Remove individual banner image fields
        unset($pageData['banner_image_url'], $pageData['banner_image_name']);

        return $pageData;
    }
}