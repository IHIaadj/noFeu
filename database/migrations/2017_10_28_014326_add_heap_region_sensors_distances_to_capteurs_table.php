<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeapRegionSensorsDistancesToCapteursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('T_CAPTEURS', function (Blueprint $table) {
            $table->string("HEAP_REG_DISTANCES");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('T_CAPTEURS', function (Blueprint $table) {
            $table->dropColumn("HEAP_REG_DISTANCES");
        });
    }
}
