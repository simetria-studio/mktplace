<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignedPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signed_plans', function (Blueprint $table) {
            $table->id();
            $table->string('pagarme_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('plan_title')->nullable();
            $table->string('select_interval')->nullable();
            $table->integer('duration_plan')->nullable();
            $table->float('plan_value')->nullable();
            $table->string('select_entrega')->nullable();
            $table->longText('cart')->nullable();
            $table->longText('product')->nullable();
            $table->longText('shipping')->nullable();
            $table->longText('observation')->nullable();
            $table->date('finish')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('signed_plans');
    }
}
