<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalDibeliToPengadaanBarangs extends Migration
{
    public function up()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->date('tanggal_dibeli')->nullable()->after('status_beli');
        });
    }

    public function down()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropColumn('tanggal_dibeli');
        });
    }
}
