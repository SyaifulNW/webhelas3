<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRencanaFromMonitoringPerbaikansTable extends Migration
{
    public function up()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->dropColumn('rencana');
        });
    }

    public function down()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->text('rencana')->nullable()->after('progress');
        });
    }
}
