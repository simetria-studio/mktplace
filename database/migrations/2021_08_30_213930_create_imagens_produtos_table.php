<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagensProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagens_produtos', function (Blueprint $table) {
            $table->id();
            $table->string('legenda');
            $table->string('texto_alternativo');
            $table->string('caminho');
            $table->string('pasta');
            $table->integer('principal')->default(0);
            $table->integer('position')->nullable();
            $table->string('produto_id');
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
        Schema::dropIfExists('imagens_produtos');
    }
}
