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
        Schema::create('program_kursuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembaga_id')->constrained()->onDelete('cascade');
            $table->string('nama_program');
            $table->string('bahasa');
            $table->string('harga');
            $table->string('durasi'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kursuses');
    }
};
