<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('satuan');
            $table->unsignedBigInteger('kategori_produk'); // Relasi ke tabel kategori
            $table->integer('stok')->default(0);
            $table->decimal('hpp', 15, 2); // Harga pokok penjualan
            $table->decimal('harga_jual', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();

            // Foreign key relasi ke tabel kategori
            $table->foreign('kategori_produk')->references('id_kategori')->on('kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
}
