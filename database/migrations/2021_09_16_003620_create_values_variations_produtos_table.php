<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValuesVariationsProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('values_variations_produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\VariationsProduto::class);
            $table->foreignIdFor(\App\Models\Attribute::class);
            $table->integer('attribute_pai_id')->nullable();
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
        Schema::dropIfExists('values_variations_produtos');
    }
}
