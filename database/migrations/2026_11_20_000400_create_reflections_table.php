<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('week', 60);                 // Contoh: Minggu ke-1 November 2026
            $table->string('mood', 60);                 // Perasaan setelah belajar
            $table->text('good');                       // Hal baik minggu ini
            $table->text('obstacle');                   // Hambatan / gangguan
            $table->text('plan');                       // Langkah perbaikan minggu depan
            // Izin siswa agar Guru BK boleh melihat refleksinya (default: boleh).
            $table->boolean('shared_with_bk')->default(true);
            $table->timestamps();

            $table->index(['student_id', 'shared_with_bk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reflections');
    }
};
