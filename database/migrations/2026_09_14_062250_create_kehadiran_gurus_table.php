<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // guru
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa'])->default('Alfa');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete(); // piket yang verifikasi
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tanggal']); // 1 guru cuma 1 baris per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_gurus');
    }
};
