<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket', 'waka', 'pengurus_kelas') DEFAULT 'guru'");
        }

        DB::table('users')->where('role', 'sekretaris')->update(['role' => 'pengurus_kelas']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'pengurus_kelas')->update(['role' => 'sekretaris']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket', 'waka', 'sekretaris') DEFAULT 'guru'");
        }
    }
};
