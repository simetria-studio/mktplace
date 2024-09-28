<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('service_name')->nullable();
            $table->string('service_price')->nullable();
            $table->string('service_quantity')->nullable();
            $table->longText('attributes')->nullable();
            $table->date('date_reservation_ini')->nullable();
            $table->date('date_reservation_fim')->nullable();
            $table->string('hour_reservation')->nullable();
            $table->string('active')->default('S');
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
        Schema::dropIfExists('service_reservations');
    }
}
