<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->nullable();
            $table->string('service_title')->nullable();
            $table->text('short_description')->nullable();
            $table->string('service_slug')->nullable();
            $table->float('preco')->nullable();
            $table->integer('vaga')->nullable();
            $table->integer('check_variation')->default(0);
            $table->integer('vaga_controller')->default(0);
            $table->integer('address_controller')->default(0);
            $table->integer('hospedagem_controller')->default(0);
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('address2')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->longText('full_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('keywords')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('whatsapp')->nullable();
            $table->longText('text_contact')->nullable();
            $table->integer('status')->default(0);
            $table->integer('ativo')->default(0);
            $table->string('selecao_hospedagem')->nullable();
            $table->string('qty_max_hospedagem')->nullable();
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
        Schema::dropIfExists('services');
    }
}
