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
    public function up()
    {
       Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            
            // Semua foreign key menggunakan UUID
            $table->uuid('product_id');
            $table->uuid('media_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Foreign key constraints untuk UUID
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->unique(['product_id', 'media_id']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_media');
    }
};
