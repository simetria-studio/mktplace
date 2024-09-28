<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->string('icon');
            $table->string('position');
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
        Schema::dropIfExists('categories');
    }
}
