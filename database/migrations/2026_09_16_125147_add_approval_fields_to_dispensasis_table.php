<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->unsignedTinyInteger('jam_pelajaran')->nullable()->after('jenis_dispensasi');

            $table->foreignId('diproses_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('status_akhir');

            $table->timestamp('diproses_at')->nullable()->after('diproses_oleh');
            $table->text('catatan_waka')->nullable()->after('diproses_at');
        });
    }

    public function down(): void
    {
        Schema::table('dispensasis', function (Blueprint $table) {
            $table->dropForeign(['diproses_oleh']);
            $table->dropColumn(['jam_pelajaran', 'diproses_oleh', 'diproses_at', 'catatan_waka']);
        });
    }
};
