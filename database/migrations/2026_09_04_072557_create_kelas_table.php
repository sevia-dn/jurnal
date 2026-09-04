<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->string('nama_kelas', 20)->unique();
            $table->string('wali_kelas', 100)->nullable();
            $table->integer('jumlah_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};