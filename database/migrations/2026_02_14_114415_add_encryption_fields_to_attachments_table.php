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
        Schema::table('lampiran', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(true)->after('path');
            $table->string('original_filename')->nullable()->after('filename'); // Simpan nama asli jika berbeda
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lampiran', function (Blueprint $table) {
            $table->dropColumn(['is_encrypted', 'original_filename']);
        });
    }
};
