<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMESTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_m_e_s', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('seller_id');
            $table->string('company_id');
            $table->string('service_id');
            $table->string('transport')->nullable();
            $table->string('agency_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('code')->nullable();
            $table->string('price');
            $table->text('package')->nullable();
            $table->string('height');
            $table->string('width');
            $table->string('length');
            $table->string('weight');
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
        Schema::dropIfExists('order_m_e_s');
    }
}
