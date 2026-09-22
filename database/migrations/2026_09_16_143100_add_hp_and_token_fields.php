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
            if (! Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('nip');
            }
        });

        Schema::table('dispensasis', function (Blueprint $table) {
            if (! Schema::hasColumn('dispensasis', 'token_approval')) {
                $table->string('token_approval', 64)->nullable()->unique()->after('status_akhir');
            }
            if (! Schema::hasColumn('dispensasis', 'dibuat_oleh')) {
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete()->after('token_approval');
            }

            // Ubah tipe data enum menjadi string agar fleksibel dengan value 'menunggu', 'disetujui', 'ditolak'
            $table->string('status_piket')->default('disetujui')->change();
            $table->string('status_waka')->default('menunggu')->change();
            $table->string('status_akhir')->default('menunggu')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });

        Schema::table('dispensasis', function (Blueprint $table) {
            $table->dropForeign(['dibuat_oleh']);
            $table->dropColumn(['token_approval', 'dibuat_oleh']);
        });
    }
};
