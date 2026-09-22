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
        Schema::create('jadwal_pikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('hari'); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
            $table->unsignedTinyInteger('bulan')->nullable(); // 1-12
            $table->unsignedSmallInteger('tahun')->nullable(); // 2026
            $table->unsignedTinyInteger('shift')->default(1); // 1: 07:00-11:00, 2: 11:00-15:00
            $table->time('jam_mulai')->default('07:00:00');
            $table->time('jam_selesai')->default('11:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pikets');
    }
};
