<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalRetiradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_retiradas', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->nullable();
            $table->integer('localidade_id')->nullable();
            $table->longText('description')->nullable();
            $table->longText('products_id')->nullable();
            $table->integer('all_products')->default(1);
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
        Schema::dropIfExists('local_retiradas');
    }
}
