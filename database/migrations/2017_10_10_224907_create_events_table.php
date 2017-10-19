<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('T_EVENTS', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("ID_CAPTEUR");
            $table->float("TEMPERATURE");
            $table->float("HUMIDITE");
            $table->boolean("SMOKE");
            $table->float("LUMINOSITE");
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
        Schema::dropIfExists('events');
    }
}
