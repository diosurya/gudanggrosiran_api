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
        Schema::create('mutations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('jumlah_saldo_tf')->nullable();
            $table->unsignedBigInteger('id_pengirim')->nullable();
            $table->foreign('id_pengirim')->references('id')->on('akuns')->nullable();
            $table->bigInteger('saldo_awal_pengirim')->nullable();
            $table->bigInteger('saldo_akhir_pengirim')->nullable();
            $table->unsignedBigInteger('id_penerima')->nullable();
            $table->foreign('id_penerima')->references('id')->on('akuns')->nullable();
            $table->bigInteger('saldo_awal_penerima')->nullable();
            $table->bigInteger('saldo_akhir_penerima')->nullable();
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
        Schema::dropIfExists('mutations');
    }
};
