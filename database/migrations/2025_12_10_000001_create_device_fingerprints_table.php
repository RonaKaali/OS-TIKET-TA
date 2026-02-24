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
        Schema::create('device_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('fingerprint', 64)->index(); // SHA256 hash
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('timezone')->nullable();
            $table->integer('trust_score')->default(50); // 0-100
            $table->timestamp('registered_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->json('metadata')->nullable(); // Additional device info
            $table->timestamps();

            // Unique constraint: satu user tidak bisa punya fingerprint yang sama
            $table->unique(['user_id', 'fingerprint']);
            
            // Index untuk query berdasarkan user dan trust score
            $table->index(['user_id', 'trust_score']);
            $table->index('last_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_fingerprints');
    }
};

