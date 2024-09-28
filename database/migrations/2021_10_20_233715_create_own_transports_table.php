<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('own_transports', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->string('estado');
            $table->string('cidade');
            $table->integer('toda_cidade')->default(0);
            $table->string('bairro');
            $table->float('valor_entrega')->nullable();
            $table->string('tempo_entrega')->nullable();
            $table->string('tempo')->nullable();
            $table->string('semana')->nullable();
            $table->string('descricao')->nullable();
            $table->integer('frete_gratis')->default(0);
            $table->float('valor_minimo')->nullable();
            $table->integer('em_todas_cidades')->default(0);
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
        Schema::dropIfExists('own_transports');
    }
}
