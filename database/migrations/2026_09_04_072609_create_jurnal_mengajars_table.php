<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_mengajars', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->foreignId('id_guru')->constrained('gurus', 'id_guru')->onDelete('cascade');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('jam_ke');
            $table->string('materi', 200)->nullable();
            $table->integer('jumlah_hadir')->default(0)->nullable();
            $table->integer('jumlah_tidak_hadir')->default(0)->nullable();
            $table->enum('status_kehadiran_guru', ['Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan'])->default('Hadir');
            $table->string('catatan', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajars');
    }
};