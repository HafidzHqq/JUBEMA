<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('topic', 100);               // Topik yang ingin didiskusikan
            $table->string('mode', 100);                // Tatap muka / daring / dst.
            $table->text('message');                    // Cerita singkat kendala
            $table->enum('status', [
                'terkirim',
                'menunggu_jadwal_bk',
                'sudah_dijadwalkan',
            ])->default('terkirim');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
