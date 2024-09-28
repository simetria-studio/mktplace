<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_configs', function (Blueprint $table) {
            $table->id();
            $table->string('page')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_path')->nullable();
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
        Schema::dropIfExists('seo_configs');
    }
}
