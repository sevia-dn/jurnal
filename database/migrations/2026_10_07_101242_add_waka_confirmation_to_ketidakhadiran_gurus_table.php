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
        Schema::table('ketidakhadiran_gurus', function (Blueprint $table) {
            $table->enum('status_konfirmasi_waka', ['pending', 'dikonfirmasi', 'ditolak'])
                ->default('pending')
                ->after('status');
            $table->foreignId('dikonfirmasi_oleh_waka')
                ->nullable()
                ->after('status_konfirmasi_waka')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('dikonfirmasi_waka_pada')
                ->nullable()
                ->after('dikonfirmasi_oleh_waka');
            $table->text('catatan_waka')
                ->nullable()
                ->after('dikonfirmasi_waka_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ketidakhadiran_gurus', function (Blueprint $table) {
            $table->dropForeign(['dikonfirmasi_oleh_waka']);
            $table->dropColumn([
                'status_konfirmasi_waka',
                'dikonfirmasi_oleh_waka',
                'dikonfirmasi_waka_pada',
                'catatan_waka',
            ]);
        });
    }
};
