<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'mfa_backup_codes')) {
                $table->json('mfa_backup_codes')->nullable()->after('mfa_enabled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'mfa_backup_codes')) {
                $table->dropColumn('mfa_backup_codes');
            }
        });
    }
};
