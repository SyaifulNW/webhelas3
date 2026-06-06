<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSdmFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('role');
            $table->string('tipe_kontrak')->nullable()->after('divisi');
            $table->string('status_sdm')->default('Aktif')->after('tipe_kontrak');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'tipe_kontrak', 'status_sdm']);
        });
    }
}
