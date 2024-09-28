<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_configs', function (Blueprint $table) {
            $table->id();
            $table->string('local')->nullable();
            $table->string('link')->nullable();
            $table->integer('new_tab')->default(0);
            $table->string('file_name')->nullable();
            $table->string('path_file')->nullable();
            $table->string('url_file')->nullable();
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
        Schema::dropIfExists('banner_configs');
    }
}
