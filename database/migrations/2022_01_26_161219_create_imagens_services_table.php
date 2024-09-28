<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagensServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagens_services', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->nullable();
            $table->string('legenda');
            $table->string('texto_alternativo');
            $table->string('caminho');
            $table->string('pasta');
            $table->integer('principal')->default(0);
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('imagens_services');
    }
}
