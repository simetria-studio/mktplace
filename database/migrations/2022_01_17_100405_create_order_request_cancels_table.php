<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRequestCancelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_request_cancels', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('title')->nullable();
            $table->string('reason')->nullable();
            $table->string('bank_code_id')->nullable();
            $table->string('agencia')->nullable();
            $table->string('agencia_dv_id')->nullable();
            $table->string('conta_id')->nullable();
            $table->string('conta_dv_id')->nullable();
            $table->string('type')->nullable();
            $table->string('document_number_id')->nullable();
            $table->string('legal_name_id')->nullable();
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
        Schema::dropIfExists('order_request_cancels');
    }
}
