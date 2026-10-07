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
            $table->foreignId('periode_id')
                ->nullable()
                ->after('sumber')
                ->constrained('periode_ketidakhadiran_siswas')
                ->nullOnDelete();
            $table->boolean('is_multi_day')
                ->default(false)
                ->after('periode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piket_kehadiran_siswas', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn(['periode_id', 'is_multi_day']);
        });
    }
};
