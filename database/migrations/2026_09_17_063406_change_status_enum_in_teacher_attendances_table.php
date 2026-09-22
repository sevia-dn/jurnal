<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL ALTER COLUMN untuk ENUM — perlu raw SQL karena Blueprint tidak support modify ENUM di semua versi
        DB::statement("ALTER TABLE teacher_attendances MODIFY COLUMN status ENUM('Hadir', 'Tidak Hadir') NOT NULL DEFAULT 'Hadir'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE teacher_attendances MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Tanpa Keterangan') NOT NULL DEFAULT 'Hadir'");
    }
};
