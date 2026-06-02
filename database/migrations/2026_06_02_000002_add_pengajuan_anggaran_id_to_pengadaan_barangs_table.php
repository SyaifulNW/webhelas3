<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengajuanAnggaranIdToPengadaanBarangsTable extends Migration
{
    public function up()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('pengajuan_anggaran_id')->nullable()->after('bukti_transfer');
            $table->foreign('pengajuan_anggaran_id')
                  ->references('id')
                  ->on('pengajuan_anggarans')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_anggaran_id']);
            $table->dropColumn('pengajuan_anggaran_id');
        });
    }
}
