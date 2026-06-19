<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSubroleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function columnExists($table, $column)
    {
        return Schema::hasColumn($table, $column);
    }

    public function up()
    {
        if (!$this->columnExists('users', 'subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('subrole')->nullable()->after('role');
            });
        }

        // Seed initial subroles and WA numbers for existing users
        // 1. Yasmin (ID: 3)
        DB::table('users')->where('id', 3)->update([
            'subrole' => json_encode(['cs_supervisor', 'gantt_cross_view', 'finance_access', 'cs_pusat', 'cs_rotasi']),
            'wa' => '088228814769'
        ]);

        // 2. Linda (ID: 2)
        DB::table('users')->where('id', 2)->update([
            'subrole' => json_encode(['sales_all_view', 'gantt_cross_view', 'cs_pusat', 'cs_rotasi']),
            'wa' => '08561490495'
        ]);

        // 3. Shafa Zahra (ID: 4)
        DB::table('users')->where('id', 4)->update([
            'subrole' => json_encode(['cs_pusat', 'cs_rotasi']),
            'wa' => '089602710354'
        ]);

        // 4. Arifa (ID: 10)
        DB::table('users')->where('id', 10)->update([
            'subrole' => json_encode(['cs_pusat'])
        ]);

        // 5. Rafi (ID: 13) - Rafi is operasional
        DB::table('users')->where('id', 13)->update([
            'subrole' => json_encode(['operasional_rafi'])
        ]);

        // 6. Fitra Jaya Saleh (ID: 1)
        DB::table('users')->where('id', 1)->update([
            'subrole' => json_encode(['exempt_transfer'])
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->columnExists('users', 'subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('subrole');
            });
        }
    }
}
