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
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('pengguna')->onDelete('set null');
            $table->string('event_type', 100)->index(); // auth_login, auth_failed, device_registered, anomaly_detected, etc
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low')->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint', 64)->nullable()->index();
            $table->json('context')->nullable(); // Additional context data
            $table->text('message')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            $table->integer('risk_score')->nullable(); // 0-100
            $table->timestamp('created_at');

            // Index untuk query berdasarkan user dan waktu
            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};

