<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeranjangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->unsignedBigInteger('user_id'); // Relasi ke tabel pengguna
            $table->unsignedBigInteger('produk_id'); // Relasi ke tabel produk
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();

            // Foreign key relasi ke tabel pengguna
            $table->foreign('user_id')->references('user_id')->on('tb_user')->onDelete('cascade');
            // Foreign key relasi ke tabel produk
            $table->foreign('produk_id')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keranjang');
    }
}
