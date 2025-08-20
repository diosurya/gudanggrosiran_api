<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * PRODUCT CATEGORIES
         */
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // SEO fields untuk kategori
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();

            $table->index('parent_id', 'idx_product_categories_parent_id');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign('parent_id', 'fk_product_categories_parent_id')
                ->references('id')
                ->on('product_categories')
                ->onDelete('set null');
        });


        /**
         * BRANDS
         */
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            
            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            $table->timestamps();
        });

        /**
         * PRODUCT SUBCATEGORIES (Enhanced)
         */
        Schema::create('product_subcategories', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('category_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->index(['category_id', 'is_active']);
        });

        /**
         * PRODUCTS (Enhanced)
         */
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('category_id');
            $table->uuid('subcategory_id')->nullable();
            $table->uuid('brand_id')->nullable();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('specifications')->nullable(); 

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Schema.org structured data
            $table->json('structured_data')->nullable();

            // Product details
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable(); // untuk margin calculation
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0); // minimum stock alert
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_backorder')->default(false);
            
            // Dimensions & shipping
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->uuid('shipping_class_id')->nullable();
            
            // Product flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_downloadable')->default(false);
            $table->boolean('requires_shipping')->default(true);
            
            // Status & visibility
            $table->enum('status', ['draft','published','archived','out_of_stock'])->default('draft');
            $table->enum('visibility', ['public','private','password','hidden'])->default('public');
            $table->string('password')->nullable();
            
            // Analytics & marketing
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('purchase_count')->default(0);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('product_subcategories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['status', 'visibility', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('average_rating');
            $table->fullText(['title', 'description']);
        });

        /**
         * PRODUCT ATTRIBUTES (for filtering)
         */
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('name'); // Color, Size, Material, etc.
            $table->string('slug');
            $table->enum('type', ['text', 'number', 'select', 'multiselect', 'boolean', 'date']);
            $table->json('options')->nullable(); // for select/multiselect
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        /**
         * PRODUCT ATTRIBUTE VALUES
         */
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('product_id');
            $table->uuid('attribute_id');
            $table->text('data');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            
            $table->unique(['product_id', 'attribute_id']);
        });

        /**
         * PRODUCT VARIANTS (Enhanced)
         */
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('product_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->json('attributes'); // {'color': 'red', 'size': 'M'}
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'is_active']);
        });

        /**
         * PRODUCT IMAGES (Enhanced)
         */
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('product_id');
            $table->uuid('variant_id')->nullable(); // untuk variant-specific images
            $table->string('path');
            $table->string('alt_text')->nullable(); // untuk SEO
            $table->string('title')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['product_id', 'sort_order']);
        });

        /**
         * PRODUCT REVIEWS
         */
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('product_id');
            $table->uuid('user_id')->nullable(); // guest reviews allowed
            $table->string('name'); // reviewer name
            $table->string('email');
            $table->integer('rating'); // 1-5
            $table->text('title');
            $table->longText('comment');
            $table->json('images')->nullable(); // review images
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'is_approved', 'rating']);
        });

        /**
         * BLOG CATEGORIES (Enhanced)
         */
         Schema::create('blog_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();

            $table->index('parent_id', 'idx_blog_categories_parent_id');
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->foreign('parent_id', 'fk_blog_categories_parent_id')
                ->references('id')
                ->on('blog_categories')
                ->onDelete('set null');
        });

        /**
         * BLOGS (Enhanced)
         */
        Schema::create('blogs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('category_id');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->integer('reading_time')->default(0); // minutes

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Schema.org structured data
            $table->json('structured_data')->nullable();

            $table->enum('status', ['draft','published','archived','scheduled'])->default('draft');
            $table->uuid('author_id')->nullable();
            
            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            
            // Publishing
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('cascade');
            $table->index(['status', 'published_at']);
            $table->fullText(['title', 'content', 'excerpt']);
        });

        /**
         * BLOG IMAGES (Enhanced)
         */
        Schema::create('blog_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('blog_id');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });

        /**
         * TAGS (Enhanced)
         */
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // hex color for UI
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        /**
         * PRODUCT <-> TAG (many-to-many)
         */
        Schema::create('product_tag', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('tag_id');
            $table->primary(['product_id', 'tag_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        /**
         * BLOG <-> TAG (many-to-many)
         */
        Schema::create('blog_tag', function (Blueprint $table) {
            $table->uuid('blog_id');
            $table->uuid('tag_id');
            $table->primary(['blog_id', 'tag_id']);

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        /**
         * SEO REDIRECTS (untuk maintain SEO rankings)
         */
        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('old_url');
            $table->string('new_url');
            $table->integer('status_code')->default(301); // 301, 302
            $table->integer('hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('old_url');
            $table->index('is_active');
        });

        /**
         * SITEMAPS
         */
        Schema::create('sitemaps', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('type'); // products, blogs, categories
            $table->string('path'); // sitemap file path
            $table->integer('url_count');
            $table->timestamp('last_generated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sitemaps');
        Schema::dropIfExists('seo_redirects');
        Schema::dropIfExists('blog_tag');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('blog_images');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_subcategories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_categories');
    }
};
