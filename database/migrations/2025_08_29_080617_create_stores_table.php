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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Store Information
            $table->string('code', 50)->unique()->comment('Unique store code for reference');
            $table->string('name', 150)->index();
            $table->string('legal_name', 200)->nullable()->comment('Registered legal name of the store entity');
            $table->text('description')->nullable();

            // Contact Information
            $table->string('email', 150)->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->string('fax', 30)->nullable();

            // Address (with international standard)
            $table->text('address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable()->index();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable()->index();
            $table->string('country', 100)->default('Indonesia')->index();

            // Geolocation (for maps / logistics)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Business Info
            $table->string('tax_number', 50)->nullable()->comment('NPWP/VAT/GST ID');
            $table->enum('timezone', timezone_identifiers_list())->default('Asia/Jakarta');

            // Operational
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_head_office')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Audit & Ownership
            $table->uuid('created_by')->nullable()->index();
            $table->uuid('updated_by')->nullable()->index();
            $table->uuid('deleted_by')->nullable()->index();

            // Laravel default
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
