<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('sequence');
            $table->string('seller_id');
            $table->string('product_id');
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->float('product_price')->nullable();
            $table->string('quantity')->nullable();
            $table->float('product_weight')->nullable();
            $table->string('product_height')->nullable();
            $table->string('product_width')->nullable();
            $table->string('product_length')->nullable();
            $table->string('product_sales_unit')->nullable();
            $table->text('attributes')->nullable();
            $table->float('discount')->nullable();
            $table->text('note')->nullable();
            $table->string('active')->default('S');
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
        Schema::dropIfExists('order_products');
    }
}
