<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'acknowledged_at')) {
                $table->timestamp('acknowledged_at')->nullable()->after('assigned_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (Schema::hasColumn('tiket', 'acknowledged_at')) {
                $table->dropColumn('acknowledged_at');
            }
        });
    }
};
