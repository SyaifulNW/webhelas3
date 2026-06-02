<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsInventoryCreatedToPengadaanBarangs extends Migration
{
    public function up()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->boolean('is_inventory_created')->default(false)->after('acc');
        });
    }

    public function down()
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropColumn('is_inventory_created');
        });
    }
}
