<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTanggalMulaiFromMonitoringPerbaikans extends Migration
{
    public function up()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->dropColumn('tanggal_mulai');
        });
    }

    public function down()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('fasilitas');
        });
    }
}
