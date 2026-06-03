<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusBeliToPengadaanBarangs extends Migration
{
    public function up()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->string('status_beli')->default('Belum Dibeli')->after('acc');
        });
    }

    public function down()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropColumn('status_beli');
        });
    }
}
