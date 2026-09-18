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
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            $table->string('lampiran')->nullable()->after('catatan');
            $table->enum('status_validasi', ['belum_divalidasi', 'disetujui', 'ditolak'])->default('belum_divalidasi')->after('lampiran');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->timestamp('divalidasi_pada')->nullable()->after('catatan_validasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            $table->dropColumn(['lampiran', 'status_validasi', 'catatan_validasi', 'divalidasi_pada']);
        });
    }
};
