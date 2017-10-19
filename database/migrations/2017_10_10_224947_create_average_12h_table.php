<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAverage12hTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('T_AVERAGE_12H', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("ID_CAPTEUR");
            $table->float("AVG_TEMP");
            $table->float("AVG_HUM");
            $table->float("AVG_LUM");
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
        Schema::dropIfExists('average_12h');
    }
}
