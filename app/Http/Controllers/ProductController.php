<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Product;


class ProductController extends Controller
{
 public function index(Request $request): JsonResponse
    {
        $query = DB::table('products')
            ->select('products.*')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id');

        if ($request->has('search')) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->join('category_product', 'category_product.product_id', '=', 'products.id')
                  ->where('category_product.category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }

        if ($request->has('status')) {
            $query->where('products.status', $request->status);
        }

        if ($request->has('featured')) {
            $query->where('products.is_featured', $request->boolean('featured'));
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $categories = DB::table('category_product')
            ->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->where('category_product.product_id', $id)
            ->get();

        $tags = DB::table('product_tag')
            ->join('tags', 'tags.id', '=', 'product_tag.tag_id')
            ->where('product_tag.product_id', $id)
            ->get();

        $images = DB::table('product_images')->where('product_id', $id)->get();
        $variants = DB::table('product_variants')->where('product_id', $id)->get();

        $reviews = DB::table('reviews')
            ->where('product_id', $id)
            ->where('is_approved', true)
            ->get();

        $faqs = DB::table('faqs')
            ->where('product_id', $id)
            ->where('is_active', true)
            ->get();

        $related = DB::table('related_products')
            ->join('products as p', 'p.id', '=', 'related_products.related_id')
            ->where('related_products.product_id', $id)
            ->get();

        return response()->json([
            'product' => $product,
            'categories' => $categories,
            'tags' => $tags,
            'images' => $images,
            'variants' => $variants,
            'reviews' => $reviews,
            'faqs' => $faqs,
            'related_products' => $related,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        $id = DB::table('products')->insertGetId($validated);

        if ($request->has('category_products')) {
            foreach ($request->category_products as $categoryId) {
                DB::table('category_product')->insert([
                    'product_id' => $id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        if ($request->has('tags')) {
            foreach ($request->tags as $tagId) {
                DB::table('product_tag')->insert([
                    'product_id' => $id,
                    'tag_id' => $tagId,
                ]);
            }
        }

        if ($request->has('images')) {
            foreach ($request->images as $imageData) {
                DB::table('product_images')->insert([
                    'product_id' => $id,
                    'image_url' => $imageData['image_url'],
                    'alt_text' => $imageData['alt_text'] ?? null,
                ]);
            }
        }

        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                DB::table('product_variants')->insert([
                    'product_id' => $id,
                    'name' => $variantData['name'],
                    'price' => $variantData['price'] ?? null,
                ]);
            }
        }

        return $this->show($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'status' => 'in:active,inactive,draft',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        DB::table('products')->where('id', $id)->update($validated);

        if ($request->has('category_products')) {
            DB::table('category_product')->where('product_id', $id)->delete();
            foreach ($request->category_products as $categoryId) {
                DB::table('category_product')->insert([
                    'product_id' => $id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        if ($request->has('tags')) {
            DB::table('product_tag')->where('product_id', $id)->delete();
            foreach ($request->tags as $tagId) {
                DB::table('product_tag')->insert([
                    'product_id' => $id,
                    'tag_id' => $tagId,
                ]);
            }
        }

        return $this->show($id);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('products')->where('id', $id)->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
