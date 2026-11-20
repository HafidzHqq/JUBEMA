<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('day', 10);                  // Senin ... Minggu
            $table->string('subject', 60);              // Mata Pelajaran / Kegiatan
            $table->string('title', 120);               // Target utama tugas
            $table->string('priority', 40);             // Penting & Mendesak, dst.
            $table->string('time', 60);                 // Estimasi waktu & waktu mulai
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index(['student_id', 'day']);
            $table->index(['student_id', 'completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
