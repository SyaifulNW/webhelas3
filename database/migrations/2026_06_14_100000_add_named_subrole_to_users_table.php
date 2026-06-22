<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNamedSubroleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'named_subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('named_subrole')->nullable()->after('subrole');
            });
        }

        // Seed named sub-roles for existing users based on their permission flags
        // Yasmin (ID:3): has cs_supervisor, gantt_cross_view, finance_kecil → HRD
        DB::table('users')->where('id', 3)->update([
            'named_subrole' => json_encode(['hrd'])
        ]);

        // Linda (ID:2): has sales_all_view, finance_access, gantt_cross_view, cs_supervisor → Keuangan
        DB::table('users')->where('id', 2)->update([
            'named_subrole' => json_encode(['keuangan'])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'named_subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('named_subrole');
            });
        }
    }
}
