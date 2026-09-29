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
        Schema::create('persetujuan_jurnal_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas', 'id_kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('disetujui_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamp('disetujui_pada');

            $table->unique(['kelas_id', 'tanggal']);
            $table->index(['tanggal', 'disetujui_oleh']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_jurnal_kelas');
    }
};
