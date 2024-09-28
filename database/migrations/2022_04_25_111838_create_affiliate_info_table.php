<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('bank')->nullable();
            $table->string('branch_number')->nullable();
            $table->string('branch_check_digit')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_check_digit')->nullable();
            $table->string('type')->nullable();
            $table->string('holder_document')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('holder_type')->nullable();
            $table->string('wallet_id')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('affiliate_infos');
    }
}
