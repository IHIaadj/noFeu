<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthly12hAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("archiveSQL")->create('T_MONTHLY_12H_AVERAGE', function (Blueprint $table) {
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
        Schema::dropIfExists('monthly_12h_averages');
    }
}
