<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatePsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_ps', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->nullable();
            $table->integer('affiliate_id')->nullable();
            $table->integer('affiliate_item')->nullable();
            $table->string('url')->nullable();
            $table->string('codigo')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('affiliate_ps');
    }
}
