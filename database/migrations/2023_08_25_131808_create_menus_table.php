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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('menu_id');
            $table->unsignedBigInteger('id_level');
            $table->string('menu_name');
            $table->string('menu_link');
            $table->string('menu_icon');
            $table->string('parent_id');
            $table->string('create_by');
            $table->string('create_date');
            $table->string('delete_mark');
            $table->string('update_by');
            $table->string('update_date');
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
        Schema::dropIfExists('menus');
    }
};
