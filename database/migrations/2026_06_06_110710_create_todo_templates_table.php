<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('todo_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['harian', 'mingguan', 'bulanan']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('todo_templates');
    }
}
