<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameSubroleToHakAkses extends Migration
{
    public function up()
    {
        // 1. subrole (permission flags) -> hak_akses
        if ($this->columnExists('users', 'subrole') && !$this->columnExists('users', 'hak_akses')) {
            DB::statement('ALTER TABLE `users` CHANGE `subrole` `hak_akses` JSON NULL');
        }

        // 2. named_subrole (named sub-roles) -> subrole
        if ($this->columnExists('users', 'named_subrole') && !$this->columnExists('users', 'subrole')) {
            DB::statement('ALTER TABLE `users` CHANGE `named_subrole` `subrole` JSON NULL');
        }
    }

    public function down()
    {
        // 1. subrole -> named_subrole
        if ($this->columnExists('users', 'subrole') && !$this->columnExists('users', 'named_subrole')) {
            DB::statement('ALTER TABLE `users` CHANGE `subrole` `named_subrole` JSON NULL');
        }

        // 2. hak_akses -> subrole
        if ($this->columnExists('users', 'hak_akses') && !$this->columnExists('users', 'subrole')) {
            DB::statement('ALTER TABLE `users` CHANGE `hak_akses` `subrole` JSON NULL');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    }
}
