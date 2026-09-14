<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kelas')->nullable()->constrained('kelas', 'id_kelas')->onDelete('cascade');
            $table->foreignId('id_jurnal')->nullable()->constrained('jurnal_mengajars', 'id_jurnal')->onDelete('cascade');
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('tipe', 50)->default('info'); // validasi, izin, sakit, info
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
