<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pemecah Tugas: setiap tugas besar dipecah menjadi beberapa langkah kecil.
     */
    public function up(): void
    {
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('text', 80);
            $table->boolean('done')->default(false);
            $table->unsignedTinyInteger('position')->default(0); // urutan langkah 1,2,3
            $table->timestamps();

            $table->index(['task_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
