<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'open_house' or 'autopilot'
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->date('tanggal');
            $table->integer('target')->default(0);
            $table->integer('realisasi')->default(0);
            $table->string('evaluasi')->nullable();
            $table->string('periode', 7); // 'YYYY-MM'
            $table->timestamps();

            $table->foreign('chapter_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapter_activities');
    }
}
