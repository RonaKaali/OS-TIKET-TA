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
            $t->foreignId('ticket_thread_id')->constrained('utas_tiket')->cascadeOnDelete();
            $t->string('filename');
            $t->string('mime');
            $t->unsignedBigInteger('size');
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
