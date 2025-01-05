<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->unsignedBigInteger('produk_id'); // Relasi ke produk
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2); // Harga per unit
            $table->decimal('total_harga', 15, 2); // Total harga per item
            $table->decimal('uang_diterima', 15, 2); // Uang yang diterima
            $table->decimal('kembalian', 15, 2)->nullable(); // Kembalian
            $table->timestamps();

            // Foreign key untuk relasi ke tabel produk
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
        Schema::dropIfExists('penjualan');
    }
}
