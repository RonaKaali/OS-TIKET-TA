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
        Schema::create('lampiran', function (Blueprint $t) {
            $t->id();
            $t->foreignId('id_utas_tiket')->constrained('utas_tiket')->cascadeOnDelete();
            $t->string('nama_file');
            $t->string('mime');
            $t->unsignedBigInteger('ukuran');
            $t->string('path'); // storage path (disk public)
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran');
    }
};
