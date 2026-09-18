<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (! Schema::hasColumn('jurnal_mengajars', 'jam_selesai')) {
                $table->integer('jam_selesai')->nullable()->after('jam_ke');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_mengajars', 'jam_selesai')) {
                $table->dropColumn('jam_selesai');
            }
        });
    }
};
