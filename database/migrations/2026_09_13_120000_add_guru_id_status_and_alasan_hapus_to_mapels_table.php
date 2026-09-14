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
        Schema::table('mapels', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('nama_mapel')->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('aktif')->after('guru_id');
            $table->text('alasan_hapus')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['guru_id', 'status', 'alasan_hapus']);
        });
    }
};
