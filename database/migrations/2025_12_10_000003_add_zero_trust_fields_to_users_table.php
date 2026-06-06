<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false);
            }
            if (!Schema::hasColumn('pengguna', 'mfa_secret')) {
                $table->text('mfa_secret')->nullable();
            }
            if (!Schema::hasColumn('pengguna', 'mfa_enabled_at')) {
                $table->timestamp('mfa_enabled_at')->nullable();
            }
            if (!Schema::hasColumn('pengguna', 'device_trust_threshold')) {
                $table->integer('device_trust_threshold')->default(70);
            }
            if (!Schema::hasColumn('pengguna', 'require_device_verification')) {
                $table->boolean('require_device_verification')->default(false);
            }
            if (!Schema::hasColumn('pengguna', 'ip_whitelist')) {
                $table->json('ip_whitelist')->nullable();
            }
            if (!Schema::hasColumn('pengguna', 'allow_after_hours_access')) {
                $table->boolean('allow_after_hours_access')->default(false);
            }
            if (!Schema::hasColumn('pengguna', 'last_security_event_at')) {
                $table->timestamp('last_security_event_at')->nullable();
            }
        });

        // Index terpisah agar tidak gagal jika sudah ada
        try {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->index('mfa_enabled');
            });
        } catch (\Throwable) {
            // Index sudah ada, abaikan
        }
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $columns = [
                'mfa_enabled',
                'mfa_secret',
                'mfa_enabled_at',
                'device_trust_threshold',
                'require_device_verification',
                'ip_whitelist',
                'allow_after_hours_access',
                'last_security_event_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('pengguna', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
