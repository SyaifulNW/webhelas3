<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportInventarisTable extends Migration
{
    public function up()
    {
        Schema::create('report_inventaris', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('bulan')->unsigned()->comment('1-12');
            $table->smallInteger('tahun')->unsigned();
            $table->date('tanggal_pemeriksaan');
            $table->string('nama_file');
            $table->string('file_pdf');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_inventaris');
    }
}
