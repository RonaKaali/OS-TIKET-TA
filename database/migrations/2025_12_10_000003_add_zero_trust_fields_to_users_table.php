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
        Schema::table('pengguna', function (Blueprint $table) {
            // MFA fields
            $table->boolean('mfa_enabled')->default(false)->after('remember_token');
            $table->text('mfa_secret')->nullable()->after('mfa_enabled'); // Encrypted TOTP secret
            $table->timestamp('mfa_enabled_at')->nullable()->after('mfa_secret');
            
            // Device trust fields
            $table->integer('device_trust_threshold')->default(70)->after('mfa_enabled_at'); // User-specific threshold
            $table->boolean('require_device_verification')->default(false)->after('device_trust_threshold');
            
            // Security settings
            $table->json('ip_whitelist')->nullable()->after('require_device_verification'); // Allowed IPs for this user
            $table->boolean('allow_after_hours_access')->default(false)->after('ip_whitelist');
            $table->timestamp('last_security_event_at')->nullable()->after('allow_after_hours_access');
            
            // Index untuk query
            $table->index('mfa_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropIndex(['mfa_enabled']);
            $table->dropColumn([
                'mfa_enabled',
                'mfa_secret',
                'mfa_enabled_at',
                'device_trust_threshold',
                'require_device_verification',
                'ip_whitelist',
                'allow_after_hours_access',
                'last_security_event_at',
            ]);
        });
    }
};

