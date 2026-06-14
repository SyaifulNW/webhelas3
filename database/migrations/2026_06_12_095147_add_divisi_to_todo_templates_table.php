<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisiToTodoTemplatesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('todo_templates', 'divisi')) {
            Schema::table('todo_templates', function (Blueprint $table) {
                // Nullable so existing data is preserved; default to first tab
                $table->string('divisi')->nullable()->default('Divisi Keuangan')->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('todo_templates', 'divisi')) {
            Schema::table('todo_templates', function (Blueprint $table) {
                $table->dropColumn('divisi');
            });
        }
    }
}
