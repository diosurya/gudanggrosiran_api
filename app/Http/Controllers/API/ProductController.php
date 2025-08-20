<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $categoryId = $request->get('category_id');
            $subcategoryId = $request->get('subcategory_id');
            $brandId = $request->get('brand_id');
            $status = $request->get('status');
            $isFeatured = $request->get('is_featured');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('product_subcategories as ps', 'p.subcategory_id', '=', 'ps.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.sku',
                    'p.excerpt',
                    'p.price',
                    'p.discount_price',
                    'p.stock',
                    'p.is_featured',
                    'p.status',
                    'p.average_rating',
                    'p.review_count',
                    'p.view_count',
                    'p.purchase_count',
                    'p.published_at',
                    'p.created_at',
                    'p.updated_at',
                    'pc.name as category_name',
                    'ps.name as subcategory_name',
                    'b.name as brand_name'
                ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.title', 'like', "%{$search}%")
                      ->orWhere('p.description', 'like', "%{$search}%")
                      ->orWhere('p.sku', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $query->where('p.category_id', $categoryId);
            }

            if ($subcategoryId) {
                $query->where('p.subcategory_id', $subcategoryId);
            }

            if ($brandId) {
                $query->where('p.brand_id', $brandId);
            }

            if ($status) {
                $query->where('p.status', $status);
            }

            if (!is_null($isFeatured)) {
                $query->where('p.is_featured', $isFeatured);
            }

            if ($minPrice) {
                $query->where('p.price', '>=', $minPrice);
            }

            if ($maxPrice) {
                $query->where('p.price', '<=', $maxPrice);
            }

            // Sorting
            $allowedSortFields = ['created_at', 'title', 'price', 'average_rating', 'view_count', 'purchase_count'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy('p.' . $sortBy, $sortOrder);
            }

            $total = $query->count();
            $products = $query
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get cover images for products
            $productIds = $products->pluck('id');
            $coverImages = DB::table('product_images')
                ->whereIn('product_id', $productIds)
                ->where('is_cover', true)
                ->pluck('path', 'product_id');

            $products = $products->map(function ($product) use ($coverImages) {
                $productArray = (array) $product;
                $productArray['cover_image'] = $coverImages[$product->id] ?? null;
                $productArray['final_price'] = $product->discount_price ?? $product->price;
                return $productArray;
            });

            return response()->json([
                'success' => true,
                'data' => $products,
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
                'message' => 'Failed to fetch products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validate([
                'category_id' => 'required|uuid|exists:product_categories,id',
                'subcategory_id' => 'nullable|uuid|exists:product_subcategories,id',
                'brand_id' => 'nullable|uuid|exists:brands,id',
                'title' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'excerpt' => 'nullable|string',
                'description' => 'nullable|string',
                'specifications' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'cost_price' => 'nullable|numeric|min:0',
                'stock' => 'integer|min:0',
                'min_stock' => 'integer|min:0',
                'track_stock' => 'boolean',
                'allow_backorder' => 'boolean',
                'weight' => 'nullable|numeric|min:0',
                'length' => 'nullable|numeric|min:0',
                'width' => 'nullable|numeric|min:0',
                'height' => 'nullable|numeric|min:0',
                'is_featured' => 'boolean',
                'is_digital' => 'boolean',
                'is_downloadable' => 'boolean',
                'requires_shipping' => 'boolean',
                'status' => 'in:draft,published,archived,out_of_stock',
                'visibility' => 'in:public,private,password,hidden',
                'password' => 'nullable|string|required_if:visibility,password',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string',
                'structured_data' => 'nullable|json'
            ]);

            $data['id'] = Str::uuid();
            $data['slug'] = Str::slug($data['title']);
            
            // Check slug uniqueness
            $count = 1;
            $originalSlug = $data['slug'];
            while (DB::table('products')->where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('products')->insert($data);

            $product = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('product_subcategories as ps', 'p.subcategory_id', '=', 'ps.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.*',
                    'pc.name as category_name',
                    'ps.name as subcategory_name',
                    'b.name as brand_name'
                ])
                ->where('p.id', $data['id'])
                ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('product_subcategories as ps', 'p.subcategory_id', '=', 'ps.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.*',
                    'pc.name as category_name',
                    'ps.name as subcategory_name',
                    'b.name as brand_name'
                ])
                ->where('p.id', $id)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Get product images
            $images = DB::table('product_images')
                ->where('product_id', $id)
                ->orderBy('sort_order')
                ->get(['id', 'path', 'alt_text', 'title', 'is_cover', 'sort_order']);

            // Get product variants
            $variants = DB::table('product_variants')
                ->where('product_id', $id)
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get();

            // Get product attributes
            $attributes = DB::table('product_attribute_values as pav')
                ->join('product_attributes as pa', 'pav.attribute_id', '=', 'pa.id')
                ->where('pav.product_id', $id)
                ->select([
                    'pa.id as attribute_id',
                    'pa.name as attribute_name',
                    'pa.type as attribute_type',
                    'pav.data as value'
                ])
                ->get();

            // Get product tags
            $tags = DB::table('product_tag as pt')
                ->join('tags as t', 'pt.tag_id', '=', 't.id')
                ->where('pt.product_id', $id)
                ->select('t.id', 't.name', 't.slug', 't.color')
                ->get();

            // Get recent reviews
            $reviews = DB::table('product_reviews')
                ->where('product_id', $id)
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Increment view count
            DB::table('products')->where('id', $id)->increment('view_count');

            $productData = (array) $product;
            $productData['images'] = $images;
            $productData['variants'] = $variants;
            $productData['attributes'] = $attributes;
            $productData['tags'] = $tags;
            $productData['reviews'] = $reviews;
            $productData['final_price'] = $product->discount_price ?? $product->price;

            return response()->json([
                'success' => true,
                'data' => $productData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $exists = DB::table('products')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $data = $request->validate([
                'category_id' => 'required|uuid|exists:product_categories,id',
                'subcategory_id' => 'nullable|uuid|exists:product_subcategories,id',
                'brand_id' => 'nullable|uuid|exists:brands,id',
                'title' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku,' . $id,
                'excerpt' => 'nullable|string',
                'description' => 'nullable|string',
                'specifications' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'cost_price' => 'nullable|numeric|min:0',
                'stock' => 'integer|min:0',
                'min_stock' => 'integer|min:0',
                'track_stock' => 'boolean',
                'allow_backorder' => 'boolean',
                'weight' => 'nullable|numeric|min:0',
                'length' => 'nullable|numeric|min:0',
                'width' => 'nullable|numeric|min:0',
                'height' => 'nullable|numeric|min:0',
                'is_featured' => 'boolean',
                'is_digital' => 'boolean',
                'is_downloadable' => 'boolean',
                'requires_shipping' => 'boolean',
                'status' => 'in:draft,published,archived,out_of_stock',
                'visibility' => 'in:public,private,password,hidden',
                'password' => 'nullable|string|required_if:visibility,password',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image' => 'nullable|string',
                'structured_data' => 'nullable|json'
            ]);

            $currentProduct = DB::table('products')->where('id', $id)->first();
            
            if ($data['title'] !== $currentProduct->title) {
                $data['slug'] = Str::slug($data['title']);
                
                // Check slug uniqueness (exclude current record)
                $count = 1;
                $originalSlug = $data['slug'];
                while (DB::table('products')
                    ->where('slug', $data['slug'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            if ($data['status'] === 'published' && empty($currentProduct->published_at)) {
                $data['published_at'] = now();
            }

            $data['updated_at'] = now();

            DB::table('products')->where('id', $id)->update($data);

            $product = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('product_subcategories as ps', 'p.subcategory_id', '=', 'ps.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.*',
                    'pc.name as category_name',
                    'ps.name as subcategory_name',
                    'b.name as brand_name'
                ])
                ->where('p.id', $id)
                ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $product = DB::table('products')->where('id', $id)->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Delete related data (cascading deletes handled by foreign keys)
            DB::table('products')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured products
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);

            $products = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.price',
                    'p.discount_price',
                    'p.average_rating',
                    'p.review_count',
                    'pc.name as category_name',
                    'b.name as brand_name'
                ])
                ->where('p.is_featured', true)
                ->where('p.status', 'published')
                ->where('p.visibility', 'public')
                ->orderBy('p.created_at', 'desc')
                ->limit($limit)
                ->get();

            // Get cover images
            $productIds = $products->pluck('id');
            $coverImages = DB::table('product_images')
                ->whereIn('product_id', $productIds)
                ->where('is_cover', true)
                ->pluck('path', 'product_id');

            $products = $products->map(function ($product) use ($coverImages) {
                $productArray = (array) $product;
                $productArray['cover_image'] = $coverImages[$product->id] ?? null;
                $productArray['final_price'] = $product->discount_price ?? $product->price;
                return $productArray;
            });

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products
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

            $products = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.price',
                    'p.discount_price',
                    'p.average_rating',
                    'p.review_count',
                    'pc.name as category_name',
                    'b.name as brand_name'
                ])
                ->where('p.status', 'published')
                ->where('p.visibility', 'public')
                ->where(function ($q) use ($query) {
                    $q->whereRaw('MATCH(p.title, p.description) AGAINST(? IN BOOLEAN MODE)', [$query])
                      ->orWhere('p.title', 'like', "%{$query}%")
                      ->orWhere('p.sku', 'like', "%{$query}%");
                })
                ->orderByRaw('MATCH(p.title, p.description) AGAINST(? IN BOOLEAN MODE) DESC', [$query])
                ->orderBy('p.average_rating', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get cover images
            $productIds = $products->pluck('id');
            $coverImages = DB::table('product_images')
                ->whereIn('product_id', $productIds)
                ->where('is_cover', true)
                ->pluck('path', 'product_id');

            $products = $products->map(function ($product) use ($coverImages) {
                $productArray = (array) $product;
                $productArray['cover_image'] = $coverImages[$product->id] ?? null;
                $productArray['final_price'] = $product->discount_price ?? $product->price;
                return $productArray;
            });

            return response()->json([
                'success' => true,
                'data' => $products,
                'query' => $query
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search products: ' . $e->getMessage()
            ], 500);
        }
    }
}