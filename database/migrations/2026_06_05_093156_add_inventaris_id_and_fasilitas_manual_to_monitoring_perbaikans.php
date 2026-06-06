<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventarisIdAndFasilitasManualToMonitoringPerbaikans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_perbaikans', function (Blueprint $table) {
            $table->unsignedBigInteger('inventaris_id')->nullable()->after('fasilitas');
            $table->string('fasilitas_manual')->nullable()->after('inventaris_id');

            $table->foreign('inventaris_id')->references('id')->on('inventaris_kantors')->onDelete('set null');
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
            $table->dropForeign(['inventaris_id']);
            $table->dropColumn(['inventaris_id', 'fasilitas_manual']);
        });
    }
}
