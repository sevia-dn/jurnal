<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            if (! Schema::hasColumn('jadwal_pikets', 'tipe')) {
                $table->string('tipe', 20)->default('guru')->after('hari');
            }

            if (! Schema::hasColumn('jadwal_pikets', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('tipe');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_pikets', 'keterangan')) {
                $table->dropColumn('keterangan');
            }

            if (Schema::hasColumn('jadwal_pikets', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });
    }
};
