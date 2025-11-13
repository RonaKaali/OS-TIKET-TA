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
            $t->string('nomor_tiket')->unique(); // OST-000001
            $t->string('subjek');
            // data pelapor (guest)
            $t->string('email_pelapor');
            $t->string('nama_pelapor')->nullable();
            // relasi
            $t->foreignId('id_pengguna')->nullable()->constrained('pengguna')->nullOnDelete();          // jika pelapor punya akun
            $t->foreignId('id_departemen')->constrained('departemen')->cascadeOnDelete();
            $t->foreignId('id_topik_bantuan')->nullable()->constrained('topik_bantuan')->nullOnDelete();
            $t->foreignId('id_prioritas')->nullable()->constrained('prioritas')->nullOnDelete();
            $t->foreignId('id_status')->constrained('status')->cascadeOnDelete();
            $t->foreignId('id_rencana_sla')->nullable()->constrained('rencana_sla')->nullOnDelete();

            $t->dateTime('jatuh_tempo_pada')->nullable();
            $t->dateTime('ditutup_pada')->nullable();

            // assignment & lock
            $t->foreignId('ditugaskan_ke')->nullable()->constrained('pengguna')->nullOnDelete();
            $t->foreignId('dikunci_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $t->dateTime('dikunci_sampai')->nullable();

            $t->json('bidang_kustom')->nullable(); // nilai dari skema_formulir
            $t->timestamps();

            $t->index(['id_status', 'id_departemen']);
            $t->index('ditugaskan_ke');
            $t->index('jatuh_tempo_pada');
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
