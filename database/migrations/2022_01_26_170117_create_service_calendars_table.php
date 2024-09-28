<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_calendars', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->date('data_inicial')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('select_termino')->nullable();
            $table->integer('antecedencia')->nullable();
            $table->integer('number_select')->nullable();
            $table->string('select_control')->nullable();
            $table->string('ocorrencia')->nullable();
            $table->longText('semana')->nullable();
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
        Schema::dropIfExists('service_calendars');
    }
}
