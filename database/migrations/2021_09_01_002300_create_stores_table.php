<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('store_name');
            $table->string('store_slug');
            $table->string('post_code')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address2')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('retirada')->nullable();
            $table->string('ob_retirada')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_path_two')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
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
        Schema::dropIfExists('stores');
    }
}
