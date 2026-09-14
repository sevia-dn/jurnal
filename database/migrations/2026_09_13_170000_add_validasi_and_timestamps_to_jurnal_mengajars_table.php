<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_mengajars', 'status_validasi')) {
                $table->enum('status_validasi', ['Menunggu', 'Selesai'])->default('Menunggu')->after('status_kehadiran_guru');
            }
            if (!Schema::hasColumn('jurnal_mengajars', 'jam_mulai')) {
                $table->time('jam_mulai')->nullable()->after('jam_ke');
            }
            if (!Schema::hasColumn('jurnal_mengajars', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable()->after('jam_mulai');
            }
            if (!Schema::hasColumn('jurnal_mengajars', 'guru_inval_id')) {
                $table->foreignId('guru_inval_id')->nullable()->constrained('users')->nullOnDelete()->after('status_validasi');
            }
            if (!Schema::hasColumn('jurnal_mengajars', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            $table->dropForeign(['guru_inval_id']);
            $table->dropColumn(['status_validasi', 'jam_mulai', 'jam_selesai', 'guru_inval_id', 'created_at', 'updated_at']);
        });
    }
};
