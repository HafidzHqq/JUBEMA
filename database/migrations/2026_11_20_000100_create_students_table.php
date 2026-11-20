<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Identitas siswa JUBEMA: cukup Nama Panggilan / Kode + Kelas.
     * Tidak ada NISN, email, atau data sensitif lain (privasi 100%).
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);                 // Nama Panggilan / Kode Siswa
            $table->string('kelas', 20);                // Kelas VII | Kelas VIII | Kelas IX
            $table->rememberToken();
            $table->timestamps();

            // Satu nama panggilan unik per kelas, supaya data tidak tercampur.
            $table->unique(['name', 'kelas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
