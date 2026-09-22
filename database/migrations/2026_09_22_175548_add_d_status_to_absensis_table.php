<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa', 'D') NOT NULL DEFAULT 'Hadir'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi') NOT NULL DEFAULT 'Hadir'");
        }
    }
};
