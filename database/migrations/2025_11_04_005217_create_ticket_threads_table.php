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
            $t->foreignId('id_tiket')->constrained('tiket')->cascadeOnDelete();
            $t->enum('tipe', ['pesan', 'balasan', 'catatan']); // pesan=pelapor, balasan=agen, catatan=internal
            $t->foreignId('id_pengguna')->nullable()->constrained('pengguna')->nullOnDelete(); // agen/user
            $t->longText('isi');
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
