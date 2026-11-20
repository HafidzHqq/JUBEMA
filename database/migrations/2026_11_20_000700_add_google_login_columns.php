<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tambahan untuk "Masuk dengan Google".
     *
     * - `users`    : akun Guru BK yang masuk memakai akun Google-nya.
     * - `students` : identitas Google siswa, supaya pemilihan Nama Panggilan
     *                + Kelas (pop up) hanya muncul sekali saja.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id', 64)->nullable()->unique()->after('email');
            $table->string('avatar_url', 2048)->nullable()->after('google_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('google_id', 64)->nullable()->unique()->after('kelas');
            $table->string('google_email')->nullable()->after('google_id');
            $table->string('google_avatar', 2048)->nullable()->after('google_email');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['google_id', 'google_email', 'google_avatar']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['google_id', 'avatar_url']);
        });
    }
};