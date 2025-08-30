<?php

namespace App\Http\Controllers\Api\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Exception;

class ProductController extends Controller
{
    public function publishedIndex(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);

            $query = DB::table('products as p')
                ->leftJoin('product_categories as pc', 'p.category_id', '=', 'pc.id')
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->select([
                    'p.id',
                    'p.title',
                    'p.slug',
                    'p.excerpt',
                    'p.price',
                    'p.discount_price',
                    'p.average_rating',
                    'p.review_count',
                    'pc.name as category_name',
                    'b.name as brand_name'
                ])
                ->where('p.status', 'published')
                ->where('p.visibility', 'public')
                ->orderBy('p.published_at', 'desc');

            $total = $query->count();
            $products = $query
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

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
                'message' => 'Failed to fetch published products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show published product detail by slug (Landing)
     */
    public function publishedShow(string $slug): JsonResponse
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
                ->where('p.slug', $slug)
                ->where('p.status', 'published')
                ->where('p.visibility', 'public')
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or not published'
                ], 404);
            }

            $images = DB::table('product_images')
                ->where('product_id', $product->id)
                ->orderBy('sort_order')
                ->get();

            $variants = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->get();

            $attributes = DB::table('product_attribute_values as pav')
                ->join('product_attributes as pa', 'pav.attribute_id', '=', 'pa.id')
                ->where('pav.product_id', $product->id)
                ->select('pa.id as attribute_id', 'pa.name as attribute_name', 'pav.data as value')
                ->get();

            $tags = DB::table('product_tag as pt')
                ->join('tags as t', 'pt.tag_id', '=', 't.id')
                ->where('pt.product_id', $product->id)
                ->select('t.id', 't.name', 't.slug', 't.color')
                ->get();

            $reviews = DB::table('product_reviews')
                ->where('product_id', $product->id)
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            DB::table('products')->where('id', $product->id)->increment('view_count');

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
                'message' => 'Failed to fetch published product: ' . $e->getMessage()
            ], 500);
        }
    }
}