<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewProductsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_products_services', function (Blueprint $table) {
            $table->string('uuid')->nullable();
            $table->string('email')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('id_reference')->nullable();
            $table->string('reference_type')->nullable();
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
        Schema::dropIfExists('view_products_services');
    }
}
