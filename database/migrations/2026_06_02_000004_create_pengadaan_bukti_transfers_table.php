<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengadaanBuktiTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('pengadaan_bukti_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengadaan_barang_id');
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('pengadaan_barang_id')
                  ->references('id')
                  ->on('pengadaan_barangs')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengadaan_bukti_transfers');
    }
}
