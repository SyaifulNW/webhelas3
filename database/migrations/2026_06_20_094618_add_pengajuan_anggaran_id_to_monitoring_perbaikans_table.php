<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengajuanAnggaranIdToMonitoringPerbaikansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->unsignedBigInteger('pengajuan_anggaran_id')->nullable()->after('bukti_transfer');
            $table->foreign('pengajuan_anggaran_id')
                  ->references('id')
                  ->on('pengajuan_anggarans')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_anggaran_id']);
            $table->dropColumn('pengajuan_anggaran_id');
        });
    }
}
