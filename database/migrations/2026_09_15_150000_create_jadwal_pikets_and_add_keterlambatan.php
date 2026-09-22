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
        // Kolom keterlambatan pada tabel jurnal_mengajars.
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (! Schema::hasColumn('jurnal_mengajars', 'menit_keterlambatan')) {
                $table->integer('menit_keterlambatan')->default(0)->after('status_kehadiran_guru');
            }
            if (! Schema::hasColumn('jurnal_mengajars', 'status_keterlambatan')) {
                $table->string('status_keterlambatan', 50)->nullable()->after('menit_keterlambatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_mengajars', 'status_keterlambatan')) {
                $table->dropColumn('status_keterlambatan');
            }
            if (Schema::hasColumn('jurnal_mengajars', 'menit_keterlambatan')) {
                $table->dropColumn('menit_keterlambatan');
            }
        });

    }
};
