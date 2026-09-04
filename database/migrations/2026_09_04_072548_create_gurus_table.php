<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id('id_guru');
            $table->string('nip', 20)->unique();
            $table->string('nama_guru', 100);
            $table->string('mapel_diampu', 50);
            $table->string('no_hp', 15)->nullable();
            $table->enum('status_kepegawaian', ['PNS', 'Honorer', 'PPPK'])->default('Honorer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};