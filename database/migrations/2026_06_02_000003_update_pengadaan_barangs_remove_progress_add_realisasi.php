<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePengadaanBarangsRemoveProgressAddRealisasi extends Migration
{
    public function up()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropColumn('progress');
            $table->decimal('realisasi_dana', 15, 2)->nullable()->default(null)->after('budget');
        });
    }

    public function down()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropColumn('realisasi_dana');
            $table->string('progress')->nullable()->after('jumlah');
        });
    }
}
