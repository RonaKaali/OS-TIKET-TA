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
        Schema::create('prioritas', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->unique();     // Rendah/Normal/Tinggi/Kritis
            $t->unsignedTinyInteger('bobot'); // 1..10
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prioritas');
    }
};
