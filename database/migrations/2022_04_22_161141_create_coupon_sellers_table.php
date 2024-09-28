<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_sellers', function (Blueprint $table) {
            $table->id();
            $table->integer('coupon_id');
            $table->integer('seller_id')->nullable();
            $table->string('check_loja')->nullable();
            $table->longText('product_id')->nullable();
            $table->longText('service_id')->nullable();
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
        Schema::dropIfExists('coupon_sellers');
    }
}
