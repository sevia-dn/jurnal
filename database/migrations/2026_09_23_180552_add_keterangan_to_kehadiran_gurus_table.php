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
        Schema::table('kehadiran_gurus', function (Blueprint $table) {
            if (! Schema::hasColumn('kehadiran_gurus', 'keterangan')) {
                $table->string('keterangan', 500)->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kehadiran_gurus', function (Blueprint $table) {
            if (Schema::hasColumn('kehadiran_gurus', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
