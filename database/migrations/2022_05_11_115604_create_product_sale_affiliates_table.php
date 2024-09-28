<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSaleAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sale_affiliates', function (Blueprint $table) {
            $table->id();
            $table->integer('affiliate_id')->nullable();
            $table->integer('reference_id')->nullable();
            $table->string('type_reference')->nullable();
            $table->string('order_number')->nullable();
            $table->integer('qty')->nullable();
            $table->float('value')->nullable();
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
        Schema::dropIfExists('product_sale_affiliates');
    }
}
