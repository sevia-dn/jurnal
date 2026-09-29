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
        Schema::table('piket_kehadiran_siswas', function (Blueprint $table) {
            $table->string('sumber', 30)->default('guru_piket')->after('status');
            $table->index(['kelas_id', 'tanggal'], 'piket_kehadiran_kelas_tanggal_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piket_kehadiran_siswas', function (Blueprint $table) {
            $table->dropIndex('piket_kehadiran_kelas_tanggal_index');
            $table->dropColumn('sumber');
        });
    }
};
