<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetAndRealisasiToTodoTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('todo_templates')) {
            Schema::table('todo_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('todo_templates', 'target')) {
                    $table->string('target')->nullable()->after('deskripsi');
                }
            });
        }

        if (Schema::hasTable('todo_logs')) {
            Schema::table('todo_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('todo_logs', 'realisasi')) {
                    $table->string('realisasi')->nullable()->after('is_done');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('todo_templates')) {
            Schema::table('todo_templates', function (Blueprint $table) {
                if (Schema::hasColumn('todo_templates', 'target')) {
                    $table->dropColumn('target');
                }
            });
        }

        if (Schema::hasTable('todo_logs')) {
            Schema::table('todo_logs', function (Blueprint $table) {
                if (Schema::hasColumn('todo_logs', 'realisasi')) {
                    $table->dropColumn('realisasi');
                }
            });
        }
    }
}
