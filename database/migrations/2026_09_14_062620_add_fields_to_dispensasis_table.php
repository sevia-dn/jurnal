<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->string('jenis_dispensasi')->nullable()->after('siswa_id');
            $table->date('tanggal_selesai')->nullable()->after('tanggal');
            $table->string('bukti')->nullable()->after('alasan'); // path file upload
        });
    }

    public function down(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->dropColumn(['jenis_dispensasi', 'tanggal_selesai', 'bukti']);
        });
    }
};
