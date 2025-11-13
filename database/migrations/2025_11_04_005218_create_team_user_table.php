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
        Schema::create('tim_pengguna', function (Blueprint $t) {
            $t->id();
            $t->foreignId('id_tim')->constrained('tim')->cascadeOnDelete();
            $t->foreignId('id_pengguna')->constrained('pengguna')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['id_tim', 'id_pengguna']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tim_pengguna');
    }
};
