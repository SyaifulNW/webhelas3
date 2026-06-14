<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitToMomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('moms', 'unit')) {
            Schema::table('moms', function (Blueprint $table) {
                $table->string('unit')->default('Helas Corp')->after('status');
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
        if (Schema::hasColumn('moms', 'unit')) {
            Schema::table('moms', function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }
}
