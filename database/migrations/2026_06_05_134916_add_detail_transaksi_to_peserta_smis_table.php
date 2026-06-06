<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailTransaksiToPesertaSmisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peserta_smis', function (Blueprint $table) {
            $table->date('tanggal_spp_awal')->nullable();
            $table->json('bulan_spp_awal')->nullable();
            $table->json('detail_pembayaran_spp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peserta_smis', function (Blueprint $table) {
            $table->dropColumn(['tanggal_spp_awal', 'bulan_spp_awal', 'detail_pembayaran_spp']);
        });
    }
}
