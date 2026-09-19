<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket', 'waka', 'pengurus_kelas') DEFAULT 'guru'");
    DB::table('users')->where('role', 'sekretaris')->update(['role' => 'pengurus_kelas']);
}

public function down(): void
{
    DB::table('users')->where('role', 'pengurus_kelas')->update(['role' => 'sekretaris']);
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket', 'waka', 'sekretaris') DEFAULT 'guru'");
}
};
