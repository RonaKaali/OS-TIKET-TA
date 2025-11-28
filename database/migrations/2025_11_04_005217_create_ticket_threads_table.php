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
        Schema::create('utas_tiket', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained('tiket')->cascadeOnDelete();
            $t->enum('type', ['message', 'reply', 'note']); // message=pelapor, reply=agen, note=internal
            $t->foreignId('user_id')->nullable()->constrained('pengguna')->nullOnDelete(); // agen/user
            $t->longText('body');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utas_tiket');
    }
};
