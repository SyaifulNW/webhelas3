<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoLogsTable extends Migration
{
    public function up()
    {
        Schema::create('todo_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('user_id');
            $table->string('periode'); // Y-m-d / Y-W / Y-m
            $table->boolean('is_done')->default(false);
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('todo_templates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['template_id', 'user_id', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('todo_logs');
    }
}
