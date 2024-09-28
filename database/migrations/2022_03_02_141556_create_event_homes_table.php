<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_homes', function (Blueprint $table) {
            $table->id();
            $table->string('link')->nullable();
            $table->integer('new_tab')->default(0);
            $table->string('file_name')->nullable();
            $table->string('path_file')->nullable();
            $table->string('url_file')->nullable();
            $table->integer('posicao')->nullable();
            $table->integer('status')->default(1);
            $table->longText('descricao_curta')->nullable();
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
        Schema::dropIfExists('event_homes');
    }
}
