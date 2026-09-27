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
        Schema::table('dispensasis', function (Blueprint $table): void {
            $table->string('token_verifikasi', 64)
                ->nullable()
                ->unique()
                ->after('token_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispensasis', function (Blueprint $table): void {
            $table->dropUnique(['token_verifikasi']);
            $table->dropColumn('token_verifikasi');
        });
    }
};
