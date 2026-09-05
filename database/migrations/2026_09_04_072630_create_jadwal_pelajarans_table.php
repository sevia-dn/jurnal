<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->onDelete('cascade');
            $table->foreignId('id_mapel')->nullable()->constrained('mapels')->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->integer('jam_ke');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('mapel', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};