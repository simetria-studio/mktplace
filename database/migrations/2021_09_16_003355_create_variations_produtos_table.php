<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationsProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations_produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Produto::class);
            $table->float('preco');
            $table->string('stock')->nullable();
            $table->float('peso');
            $table->float('dimensoes_C');
            $table->float('dimensoes_L');
            $table->float('dimensoes_A');
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
        Schema::dropIfExists('variations_produtos');
    }
}
