<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $t) {
            if (!Schema::hasColumn('pengguna', 'id_organisasi')) {
                $t->foreignId('id_organisasi')->nullable()->after('id')
                    ->constrained('organisasi')->nullOnDelete();
            }

            if (!Schema::hasColumn('pengguna', 'telepon')) {
                $t->string('telepon')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $t) {
            if (Schema::hasColumn('pengguna', 'id_organisasi')) {
                $t->dropConstrainedForeignId('id_organisasi');
            }

            if (Schema::hasColumn('pengguna', 'telepon')) {
                $t->dropColumn('telepon');
            }
        });
    }
};
