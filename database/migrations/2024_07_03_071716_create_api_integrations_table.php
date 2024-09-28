<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->text('api_name')->nullable();
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->date('expires_in')->nullable();
            $table->longText('other_information')->nullable();
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
        Schema::dropIfExists('api_integrations');
    }
}
