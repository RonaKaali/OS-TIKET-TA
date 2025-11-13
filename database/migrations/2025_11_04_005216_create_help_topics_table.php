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
        Schema::create('topik_bantuan', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->foreignId('id_departemen')->constrained('departemen')->cascadeOnDelete();
            $t->json('skema_formulir')->nullable(); // JSON schema untuk custom fields portal
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topik_bantuan');
    }
};
