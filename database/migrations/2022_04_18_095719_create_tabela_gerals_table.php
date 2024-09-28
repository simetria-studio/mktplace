<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelaGeralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabela_gerals', function (Blueprint $table) {
            $table->id();
            $table->string('tabela')->nullable();
            $table->string('coluna')->nullable();
            $table->string('valor')->nullable();
            $table->longText('array_text')->nullable();
            $table->longText('long_text')->nullable();
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
        Schema::dropIfExists('tabela_gerals');
    }
}
