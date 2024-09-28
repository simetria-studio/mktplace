<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('plan_title')->nullable();
            $table->string('select_interval')->nullable();
            $table->integer('duration_plan')->nullable();
            $table->float('plan_value')->nullable();
            $table->string('select_entrega')->nullable();
            $table->text('descption_plan')->nullable();
            $table->float('peso')->nullable();
            $table->float('dimensoes_C')->nullable();
            $table->float('dimensoes_L')->nullable();
            $table->float('dimensoes_A')->nullable();
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
        Schema::dropIfExists('plan_purchases');
    }
}
