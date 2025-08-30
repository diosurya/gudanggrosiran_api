<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with([
                'category', 
                'brand', 
                'tags',
                'media',
                'variants.media' // Include variant images
            ]);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_product_id', $request->get('category_id'));
            }

            // Filter by brand
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->get('brand_id'));
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by stock status
            if ($request->has('stock_status')) {
                if ($request->get('stock_status') === 'in_stock') {
                    $query->where('stock_quantity', '>', 0);
                } elseif ($request->get('stock_status') === 'out_of_stock') {
                    $query->where('stock_quantity', '<=', 0);
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_product_id' => 'required|exists:category_products,id',
            'brand_id' => 'nullable|exists:brands,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:60', // SEO optimal length
            'meta_description' => 'nullable|string|max:160', // SEO optimal length
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|exists:media,id',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|exists:media,id',
            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string|in:index,noindex,follow,nofollow,index follow,noindex nofollow',
            'schema_type' => 'nullable|string|in:Product,VariationProduct',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'nullable|array',
            'images.*' => 'exists:media,id',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.sku' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.images' => 'nullable|array',
            'variants.*.images.*' => 'exists:media,id'
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
            $productData = $request->only([
                'name', 'description', 'short_description', 'sku', 'price', 
                'sale_price', 'stock_quantity', 'category_product_id', 'brand_id',
                'weight', 'dimensions', 'status', 'featured', 
                'meta_title', 'meta_description', 'meta_keywords',
                'og_title', 'og_description', 'og_image',
                'twitter_title', 'twitter_description', 'twitter_image',
                'canonical_url', 'robots', 'schema_type'
            ]);

            // Generate slug
            $productData['slug'] = Str::slug($request->name);
            
            // Ensure unique slug
            $originalSlug = $productData['slug'];
            $count = 1;
            while (Product::where('slug', $productData['slug'])->exists()) {
                $productData['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            $product = Product::create($productData);

            // Attach tags if provided
            if ($request->has('tags') && is_array($request->tags)) {
                $product->tags()->attach($request->tags);
            }

            // Attach images if provided
            if ($request->has('images') && is_array($request->images)) {
                foreach ($request->images as $mediaId) {
                    DB::table('product_media')->insert([
                        'product_id' => $product->id,
                        'media_id' => $mediaId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Create variants if provided
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    $variant = DB::table('product_variants')->insertGetId([
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'stock_quantity' => $variantData['stock_quantity'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Attach variant images
                    if (isset($variantData['images']) && is_array($variantData['images'])) {
                        foreach ($variantData['images'] as $mediaId) {
                            DB::table('product_variant_media')->insert([
                                'product_variant_id' => $variant,
                                'media_id' => $mediaId,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $product->load(['category', 'brand', 'tags', 'media', 'variants.media']);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'brand', 'tags', 'media'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'category_product_id' => 'sometimes|required|exists:category_products,id',
            'brand_id' => 'nullable|exists:brands,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'status' => 'sometimes|required|in:draft,published,archived',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'nullable|array',
            'images.*' => 'exists:media,id'
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
            $productData = $request->only([
                'name', 'description', 'short_description', 'sku', 'price', 
                'sale_price', 'stock_quantity', 'category_product_id', 'brand_id',
                'weight', 'dimensions', 'status', 'featured', 'meta_title', 'meta_description'
            ]);

            // Update slug if name changed
            if ($request->has('name') && $request->name !== $product->name) {
                $productData['slug'] = Str::slug($request->name);
                
                // Ensure unique slug
                $originalSlug = $productData['slug'];
                $count = 1;
                while (Product::where('slug', $productData['slug'])->where('id', '!=', $product->id)->exists()) {
                    $productData['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $product->update($productData);

            // Update tags if provided
            if ($request->has('tags')) {
                if (is_array($request->tags)) {
                    $product->tags()->sync($request->tags);
                } else {
                    $product->tags()->detach();
                }
            }

            // Update images if provided
            if ($request->has('images')) {
                // Remove existing images
                DB::table('product_media')->where('product_id', $product->id)->delete();
                
                // Add new images
                if (is_array($request->images) && !empty($request->images)) {
                    foreach ($request->images as $mediaId) {
                        DB::table('product_media')->insert([
                            'product_id' => $product->id,
                            'media_id' => $mediaId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            DB::commit();

            $product->load(['category', 'brand', 'tags', 'media']);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Remove relationships
            $product->tags()->detach();
            DB::table('product_media')->where('product_id', $product->id)->delete();
            
            $product->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update products status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'status' => 'required|in:draft,published,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Product::whereIn('id', $request->ids)
                   ->update(['status' => $request->status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Products status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update products status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
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
            $products = Product::whereIn('id', $request->ids)->get();

            foreach ($products as $product) {
                $product->tags()->detach();
                DB::table('product_media')->where('product_id', $product->id)->delete();
                $product->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Products deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}