<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->after('mapel');
            $table->text('alasan_hapus')->nullable()->after('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            $table->dropColumn(['status', 'alasan_hapus', 'created_at', 'updated_at']);
        });
    }
};
