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
        Schema::create('piket_kehadiran_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('kelas_id');
            $table->date('tanggal');
            $table->string('status', 20);
            $table->string('catatan', 255)->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['siswa_id', 'tanggal']);
            $table->foreign('siswa_id')->references('id')->on('siswas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piket_kehadiran_siswas');
    }
};
