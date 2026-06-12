<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisiToTodoTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('todo_templates', function (Blueprint $table) {
            // Nullable so existing data is preserved; default to first tab
            $table->string('divisi')->nullable()->default('Divisi Keuangan')->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('todo_templates', function (Blueprint $table) {
            $table->dropColumn('divisi');
        });
    }
}
