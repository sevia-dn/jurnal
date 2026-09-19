<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket', 'waka', 'sekretaris') DEFAULT 'guru'");
}

public function down(): void
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guru', 'piket', 'waka', 'sekretaris') DEFAULT 'guru'");
}
};
