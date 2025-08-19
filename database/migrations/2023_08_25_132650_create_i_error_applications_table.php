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
        Schema::create('i_error_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('error_date');
            $table->string('modules');
            $table->string('controller');
            $table->string('function');
            $table->string('error_line');
            $table->string('error_message');
            $table->string('status');
            $table->string('param');
            $table->string('create_date');
            $table->string('create_time');
            $table->string('delete_mark');
            $table->string('update_by');
            $table->string('update_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i_error_applications');
    }
};
