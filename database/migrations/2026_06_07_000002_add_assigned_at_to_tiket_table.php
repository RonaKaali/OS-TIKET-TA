<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (Schema::hasColumn('tiket', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
        });
    }
};
