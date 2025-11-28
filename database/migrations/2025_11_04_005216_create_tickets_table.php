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
        Schema::create('tiket', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->string('ticket_number')->unique(); // OST-000001
            $t->string('subject');
            // data pelapor (guest)
            $t->string('reporter_email');
            $t->string('reporter_name')->nullable();
            // relasi
            $t->foreignId('user_id')->nullable()->constrained('pengguna')->nullOnDelete();          // jika pelapor punya akun
            $t->foreignId('department_id')->constrained('departemen')->cascadeOnDelete();
            $t->foreignId('help_topic_id')->nullable()->constrained('topik_bantuan')->nullOnDelete();
            $t->foreignId('priority_id')->nullable()->constrained('prioritas')->nullOnDelete();
            $t->foreignId('status_id')->constrained('status')->cascadeOnDelete();
            $t->foreignId('sla_plan_id')->nullable()->constrained('rencana_sla')->nullOnDelete();

            $t->dateTime('due_at')->nullable();
            $t->dateTime('closed_at')->nullable();

            // assignment & lock
            $t->foreignId('assigned_to')->nullable()->constrained('pengguna')->nullOnDelete();
            $t->foreignId('locked_by')->nullable()->constrained('pengguna')->nullOnDelete();
            $t->dateTime('locked_until')->nullable();

            $t->json('custom_fields')->nullable(); // nilai dari skema_formulir
            $t->timestamps();

            $t->index(['status_id', 'department_id']);
            $t->index('assigned_to');
            $t->index('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
