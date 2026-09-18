<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mengajars', function (Blueprint $table) {
            $table->id('id_jadwal');

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('id_kelas')
                ->constrained('kelas', 'id_kelas')
                ->onDelete('cascade');

            $table->foreignId('id_mapel')
                ->constrained('mapels')
                ->onDelete('cascade');

            $table->enum('hari', [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat'
            ]);

            $table->integer('jam_mulai');
            $table->integer('jam_selesai');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajars');
    }
};