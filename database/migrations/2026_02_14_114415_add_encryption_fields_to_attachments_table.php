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
            if (!Schema::hasColumn('lampiran', 'is_encrypted')) {
                $table->boolean('is_encrypted')->default(true)->after('path');
            }

            if (!Schema::hasColumn('lampiran', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('filename');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lampiran', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['is_encrypted', 'original_filename'],
                fn (string $column) => Schema::hasColumn('lampiran', $column)
            ));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
