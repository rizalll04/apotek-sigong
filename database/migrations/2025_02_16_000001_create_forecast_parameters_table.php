mo<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForecastParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->decimal('optimized_alpha', 3, 2);
            $table->decimal('optimized_beta', 3, 2);
            $table->decimal('optimized_gamma', 3, 2);
            $table->decimal('mape_value', 5, 2);
            $table->text('data_quality_warning')->nullable();
            $table->timestamps();

            // Foreign key constraint to produk table
            $table->foreign('product_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forecast_parameters');
    }
}
