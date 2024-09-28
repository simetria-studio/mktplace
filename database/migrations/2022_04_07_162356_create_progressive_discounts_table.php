<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressiveDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progressive_discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->integer('discount_quantity')->nullable();
            $table->float('discount_value')->nullable();
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
        Schema::dropIfExists('progressive_discounts');
    }
}
