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
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'reporter_organization')) {
                $table->string('reporter_organization')->nullable()->after('reporter_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (Schema::hasColumn('tiket', 'reporter_organization')) {
                $table->dropColumn('reporter_organization');
            }
        });
    }
};
