<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'specifications' => $this->specifications ? json_decode($this->specifications, true) : null,

            // SEO data
            'seo' => $this->when($request->boolean('include_seo'), [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'canonical_url' => $this->canonical_url,
                'og_title' => $this->og_title,
                'og_description' => $this->og_description,
                'og_image' => $this->og_image,
                'structured_data' => $this->structured_data ? json_decode($this->structured_data, true) : null,
            ]),

            // Pricing
            'price' => [
                'regular' => (float) $this->price,
                'discount' => $this->discount_price ? (float) $this->discount_price : null,
                'cost' => $this->when($request->user()?->can('view-cost-price'), (float) $this->cost_price),
                'final' => (float) ($this->discount_price ?? $this->price),
                'discount_percentage' => $this->discount_price 
                    ? round((($this->price - $this->discount_price) / $this->price) * 100, 2)
                    : null,
                'currency' => config('app.currency', 'USD'),
                'formatted' => [
                    'regular' => $this->formatPrice($this->price),
                    'discount' => $this->discount_price ? $this->formatPrice($this->discount_price) : null,
                    'final' => $this->formatPrice($this->discount_price ?? $this->price),
                ],
            ],

            // Inventory
            'inventory' => [
                'stock' => $this->stock,
                'min_stock' => $this->min_stock,
                'track_stock' => $this->track_stock,
                'allow_backorder' => $this->allow_backorder,
                'in_stock' => $this->stock > 0 || $this->allow_backorder,
                'stock_status' => $this->getStockStatus(),
                'availability' => $this->getAvailabilityStatus(),
            ],

            // Physical properties
            'dimensions' => $this->when($this->weight || $this->length || $this->width || $this->height, [
                'weight' => $this->weight ? (float) $this->weight : null,
                'length' => $this->length ? (float) $this->length : null,
                'width' => $this->width ? (float) $this->width : null,
                'height' => $this->height ? (float) $this->height : null,
                'weight_unit' => config('app.weight_unit', 'kg'),
                'dimension_unit' => config('app.dimension_unit', 'cm'),
            ]),

            // Product flags
            'flags' => [
                'is_featured' => $this->is_featured,
                'is_digital' => $this->is_digital,
                'is_downloadable' => $this->is_downloadable,
                'requires_shipping' => $this->requires_shipping,
                'on_sale' => $this->discount_price !== null,
                'new' => $this->created_at >= now()->subDays(30),
            ],

            // Status and visibility
            'status' => $this->status,
            'visibility' => $this->visibility,

            // Analytics (public data only)
            'analytics' => [
                'view_count' => $this->view_count,
                'purchase_count' => $this->purchase_count,
                'average_rating' => $this->average_rating ? (float) $this->average_rating : 0,
                'review_count' => $this->review_count,
                'rating_distribution' => $this->when($this->relationLoaded('reviews'), 
                    $this->getRatingDistribution()
                ),
            ],

            // Relationships
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'attributes' => ProductAttributeValueResource::collection($this->whenLoaded('attributeValues')),
            'reviews' => ProductReviewResource::collection($this->whenLoaded('reviews')),

            // URLs
            'urls' => [
                'view' => route('products.show', $this->slug),
                'api' => route('api.products.show', $this->id),
                'edit' => $this->when($request->user()?->can('update', $this->resource), 
                    route('api.products.update', $this->id)
                ),
            ],

            // Timestamps
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Additional data for admin users
            'admin_data' => $this->when($request->user()?->hasRole('admin'), [
                'cost_analysis' => [
                    'margin' => $this->cost_price ? (($this->price - $this->cost_price) / $this->price) * 100 : null,
                    'profit_per_unit' => $this->cost_price ? $this->price - $this->cost_price : null,
                ],
                'seo_score' => $this->calculateSeoScore(),
                'completeness' => $this->calculateCompleteness(),
            ]),
        ];
    }

    /**
     * Additional data to be included with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'currency' => config('app.currency', 'USD'),
                'currency_symbol' => config('app.currency_symbol', '$'),
                'tax_inclusive' => config('app.prices_include_tax', false),
            ]
        ];
    }

    /**
     * Format price according to locale and currency
     */
    private function formatPrice($price): string
    {
        $symbol = config('app.currency_symbol', '$');
        return $symbol . number_format($price, 2);
    }

    /**
     * Get stock status string
     */
    private function getStockStatus(): string
    {
        if (!$this->track_stock) {
            return 'unlimited';
        }

        if ($this->stock <= 0) {
            return $this->allow_backorder ? 'backorder' : 'out_of_stock';
        }

        if ($this->stock <= $this->min_stock) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Get availability status for schema.org
     */
    private function getAvailabilityStatus(): string
    {
        $stockStatus = $this->getStockStatus();

        return match ($stockStatus) {
            'in_stock', 'unlimited' => 'https://schema.org/InStock',
            'low_stock' => 'https://schema.org/LimitedAvailability',
            'backorder' => 'https://schema.org/PreOrder',
            'out_of_stock' => 'https://schema.org/OutOfStock',
            default => 'https://schema.org/InStock',
        };
    }

    /**
     * Get rating distribution
     */
    private function getRatingDistribution(): array
    {
        if (!$this->relationLoaded('reviews')) {
            return [];
        }

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        foreach ($this->reviews as $review) {
            $distribution[$review->rating]++;
        }

        return $distribution;
    }

    /**
     * Calculate SEO score (0-100)
     */
    private function calculateSeoScore(): int
    {
        $score = 0;

        // Title (20 points)
        if ($this->title) {
            $score += 10;
            if (strlen($this->title) >= 30 && strlen($this->title) <= 60) {
                $score += 10;
            }
        }

        // Meta description (20 points)
        if ($this->meta_description) {
            $score += 10;
            if (strlen($this->meta_description) >= 120 && strlen($this->meta_description) <= 160) {
                $score += 10;
            }
        }

        // Images with alt text (15 points)
        if ($this->relationLoaded('images') && $this->images->count() > 0) {
            $score += 5;
            if ($this->images->where('alt_text', '!=', null)->count() > 0) {
                $score += 10;
            }
        }

        // Description (15 points)
        if ($this->description) {
            $score += 10;
            if (strlen($this->description) >= 300) {
                $score += 5;
            }
        }

        // Tags (10 points)
        if ($this->relationLoaded('tags') && $this->tags->count() >= 3) {
            $score += 10;
        }

        // Structured data (10 points)
        if ($this->structured_data) {
            $score += 10;
        }

        // OpenGraph data (10 points)
        if ($this->og_title && $this->og_description) {
            $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Calculate product completeness (0-100)
     */
    private function calculateCompleteness(): int
    {
        $score = 0;
        $totalFields = 15;

        // Required fields (5 points each)
        if ($this->title) $score += 5;
        if ($this->description) $score += 5;
        if ($this->price > 0) $score += 5;
        if ($this->sku) $score += 5;
        if ($this->category_id) $score += 5;

        // Optional but important fields (3 points each)
        if ($this->excerpt) $score += 3;
        if ($this->brand_id) $score += 3;
        if ($this->meta_description) $score += 3;
        if ($this->weight) $score += 3;

        // Images (15 points)
        if ($this->relationLoaded('images')) {
            $imageCount = $this->images->count();
            if ($imageCount >= 1) $score += 5;
            if ($imageCount >= 3) $score += 5;
            if ($imageCount >= 5) $score += 5;
        }

        // Tags (10 points)
        if ($this->relationLoaded('tags') && $this->tags->count() >= 2) {
            $score += 10;
        }

        // Variants (5 points)
        if ($this->relationLoaded('variants') && $this->variants->count() > 0) {
            $score += 5;
        }

        // Specifications (5 points)
        if ($this->specifications) {
            $score += 5;
        }

        return min($score, 100);
    }
}