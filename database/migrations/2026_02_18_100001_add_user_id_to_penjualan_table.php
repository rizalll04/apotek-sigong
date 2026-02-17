<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add column only if it doesn't exist to avoid duplicate errors
        if (!Schema::hasColumn('penjualan', 'user_id')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id_penjualan');
                // Attempt to add FK referencing tb_user(user_id)
                $table->foreign('user_id')->references('user_id')->on('tb_user')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            if (Schema::hasColumn('penjualan', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
