<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispensasi_id')->constrained('dispensasis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dispensasi_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensasi_siswa');
    }
};
