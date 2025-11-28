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
        Schema::create('status', function (Blueprint $t) {
            $t->id();
            $t->string('name');             // Terbuka, Menunggu Pelapor, Tertutup, Terlambat
            $t->string('slug')->unique();   // terbuka, menunggu_pelapor, tertutup, terlambat
            $t->boolean('is_closed')->default(false);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};
