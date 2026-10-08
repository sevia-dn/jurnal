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
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (! Schema::hasColumn('jurnal_mengajars', 'filled_at')) {
                $table->timestamp('filled_at')->nullable()->after('tanggal');
            }
            if (! Schema::hasColumn('jurnal_mengajars', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('filled_at');
            }
            if (! Schema::hasColumn('jurnal_mengajars', 'late_mode')) {
                $table->string('late_mode', 50)->nullable()->after('is_late');
            }
        });

        $legacyRows = DB::table('jurnal_mengajars')->whereNull('filled_at')->get();
        foreach ($legacyRows as $row) {
            $hour = ! empty($row->jam_selesai) ? min(23, max(7, (int) $row->jam_selesai + 6)) : 12;
            $timeString = str_pad((string) $hour, 2, '0', STR_PAD_LEFT).':00:00';
            DB::table('jurnal_mengajars')->where('id_jurnal', $row->id_jurnal)->update([
                'filled_at' => $row->tanggal.' '.$timeString,
                'is_late' => false,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_mengajars', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_mengajars', 'late_mode')) {
                $table->dropColumn('late_mode');
            }
            if (Schema::hasColumn('jurnal_mengajars', 'is_late')) {
                $table->dropColumn('is_late');
            }
            if (Schema::hasColumn('jurnal_mengajars', 'filled_at')) {
                $table->dropColumn('filled_at');
            }
        });
    }
};
