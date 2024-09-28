<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_items', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->nullable();
            $table->string('name')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('price_type')->nullable();
            $table->float('price')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('affiliate_items');
    }
}
