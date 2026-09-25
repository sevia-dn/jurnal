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
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            if (! Schema::hasColumn('jadwal_pikets', 'tanggal')) {
                $table->date('tanggal')->nullable()->after('hari');
                $table->index(['tanggal', 'user_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_pikets', 'tanggal')) {
                $table->dropIndex(['tanggal', 'user_id']);
                $table->dropColumn('tanggal');
            }
        });
    }
};
