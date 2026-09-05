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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change(); //email jadi opsional
            $table->string('username')->unique()->nullable()->after('name'); // Untuk honorer/sekretaris
            $table->string('nip')->nullable()->unique()->after('username'); // Untuk PNS
            $table->enum('role', ['guru', 'piket', 'waka', 'sekretaris'])->default('guru')->after('nip'); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nip', 'role']); // menghapus kolom jika migrasi di-rollback
        });
    }
};
