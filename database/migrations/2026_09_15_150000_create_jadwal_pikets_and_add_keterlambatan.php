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
        // 1. Tabel Jadwal Piket (Guru & Waka)
        if (!Schema::hasTable('jadwal_pikets')) {
            Schema::create('jadwal_pikets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
                $table->enum('tipe', ['guru', 'waka'])->default('guru');
                $table->string('keterangan', 255)->nullable();
                $table->timestamps();
            });
        }

        // 2. Kolom keterlambatan pada tabel jurnal_mengajars
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_mengajars', 'menit_keterlambatan')) {
                $table->integer('menit_keterlambatan')->default(0)->after('status_kehadiran_guru');
            }
            if (!Schema::hasColumn('jurnal_mengajars', 'status_keterlambatan')) {
                $table->string('status_keterlambatan', 50)->nullable()->after('menit_keterlambatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_mengajars', 'status_keterlambatan')) {
                $table->dropColumn('status_keterlambatan');
            }
            if (Schema::hasColumn('jurnal_mengajars', 'menit_keterlambatan')) {
                $table->dropColumn('menit_keterlambatan');
            }
        });

        Schema::dropIfExists('jadwal_pikets');
    }
};
