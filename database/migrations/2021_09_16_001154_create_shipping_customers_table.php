<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_customers', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->text('tracking_id')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('transport')->nullable();
            $table->float('price')->nullable();
            $table->string('time')->nullable();
            $table->longText('general_data')->nullable();
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
        Schema::dropIfExists('shipping_customers');
    }
}
