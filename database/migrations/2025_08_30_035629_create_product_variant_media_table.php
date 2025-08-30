<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('product_variant_media', function (Blueprint $table) {
            $table->id();
            
            // Semua foreign key menggunakan UUID
            $table->uuid('product_variant_id');
            $table->uuid('media_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Foreign key constraints untuk UUID
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->unique(['product_variant_id', 'media_id']);
            $table->index(['product_variant_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_media');
    }
};
