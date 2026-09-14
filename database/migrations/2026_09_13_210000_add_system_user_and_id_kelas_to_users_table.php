<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_system_user')) {
                $table->boolean('is_system_user')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'id_kelas')) {
                $table->foreignId('id_kelas')->nullable()->after('mapel_id')->constrained('kelas', 'id_kelas')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_kelas')) {
                $table->dropForeign(['id_kelas']);
                $table->dropColumn('id_kelas');
            }
            if (Schema::hasColumn('users', 'is_system_user')) {
                $table->dropColumn('is_system_user');
            }
        });
    }
};
