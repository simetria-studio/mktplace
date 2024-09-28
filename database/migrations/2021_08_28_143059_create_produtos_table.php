<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug');
            $table->string('descricao_curta');
            $table->string('preco');
            $table->text('descricao_completa');

            $table->integer('perecivel')->default(0)->nullable();

            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();

            $table->string('stock_controller')->nullable();
            $table->string('stock')->nullable();

            $table->integer('status')->default(0);
            $table->string('ativo', 1)->default('S');

            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_path')->nullable();

            $table->foreignIdFor(\App\Models\Seller::class);

            // TODO analisar melhor a parte de attributos
            // $table->string('atributos');

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
        Schema::dropIfExists('produtos');
    }
}
