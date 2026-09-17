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
        Schema::table('dispensasis', function (Blueprint $table) {
            if (Schema::hasColumn('dispensasis', 'jam_pelajaran')) {
                $table->renameColumn('jam_pelajaran', 'jam_ke_mulai');
            } elseif (!Schema::hasColumn('dispensasis', 'jam_ke_mulai')) {
                $table->unsignedTinyInteger('jam_ke_mulai')->nullable()->after('jenis_dispensasi');
            }

            if (!Schema::hasColumn('dispensasis', 'jam_ke_selesai')) {
                $table->unsignedTinyInteger('jam_ke_selesai')->nullable()->after('jam_ke_mulai');
            }

            if (!Schema::hasColumn('dispensasis', 'tipe_dispensasi')) {
                $table->enum('tipe_dispensasi', [
                    'satu_hari',
                    'per_jam',
                    'multi_hari_penuh',
                    'multi_hari_per_jam'
                ])->default('satu_hari')->after('jenis_dispensasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            if (Schema::hasColumn('dispensasis', 'tipe_dispensasi')) {
                $table->dropColumn('tipe_dispensasi');
            }

            if (Schema::hasColumn('dispensasis', 'jam_ke_selesai')) {
                $table->dropColumn('jam_ke_selesai');
            }

            if (Schema::hasColumn('dispensasis', 'jam_ke_mulai')) {
                $table->renameColumn('jam_ke_mulai', 'jam_pelajaran');
            }
        });
    }
};
