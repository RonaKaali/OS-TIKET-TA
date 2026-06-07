<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'last_gps')) {
                $table->json('last_gps')->nullable();
            }
            if (!Schema::hasColumn('pengguna', 'last_gps_at')) {
                $table->timestamp('last_gps_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'last_gps')) {
                $table->dropColumn('last_gps');
            }
            if (Schema::hasColumn('pengguna', 'last_gps_at')) {
                $table->dropColumn('last_gps_at');
            }
        });
    }
};
