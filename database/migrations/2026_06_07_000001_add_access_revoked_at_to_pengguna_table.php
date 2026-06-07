<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'access_revoked_at')) {
                $table->timestamp('access_revoked_at')->nullable()->after('last_security_event_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'access_revoked_at')) {
                $table->dropColumn('access_revoked_at');
            }
        });
    }
};
