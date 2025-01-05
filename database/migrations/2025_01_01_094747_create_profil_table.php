<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profil', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Kolom untuk menyimpan ID user
            $table->string('alamat');  // Alamat pengguna
            $table->date('tanggal_lahir');  // Tanggal lahir pengguna
            $table->string('foto')->nullable();  // Foto profil (opsional)
            $table->timestamps();

            // Menambahkan foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('tb_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil');
    }
}
