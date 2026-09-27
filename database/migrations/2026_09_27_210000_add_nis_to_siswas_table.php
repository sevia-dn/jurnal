<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (! Schema::hasColumn('siswas', 'nis')) {
                $table->string('nis', 30)->nullable()->after('nisn');
            }
            if (! Schema::hasColumn('siswas', 'nisn')) {
                $table->string('nisn', 30)->nullable()->after('kelas_id');
            }
        });

        // Sinkronkan nilai nis dan nisn agar dua-duanya selalu terisi
        if (Schema::hasColumn('siswas', 'nis') && Schema::hasColumn('siswas', 'nisn')) {
            DB::statement("UPDATE siswas SET nis = nisn WHERE (nis IS NULL OR nis = '') AND nisn IS NOT NULL");
            DB::statement("UPDATE siswas SET nisn = nis WHERE (nisn IS NULL OR nisn = '') AND nis IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'nis')) {
                $table->dropColumn('nis');
            }
        });
    }
};
