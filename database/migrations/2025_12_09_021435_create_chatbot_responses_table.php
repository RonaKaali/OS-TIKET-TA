<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_responses', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->comment('Kata kunci atau pertanyaan yang akan dicocokkan');
            $table->text('response')->comment('Jawaban yang akan dikirim bot');
            $table->boolean('is_active')->default(true)->comment('Status aktif/tidak aktif');
            $table->integer('priority')->default(0)->comment('Prioritas matching (lebih tinggi = dicocokkan lebih dulu)');
            $table->string('match_type')->default('contains')->comment('Tipe pencocokan: contains, exact, starts_with');
            $table->timestamps();
            
            $table->index('keyword');
            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_responses');
    }
};
