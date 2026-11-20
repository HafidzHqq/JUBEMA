<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('respondent_name', 80)->nullable()->after('student_id');
            $table->string('respondent_kelas', 20)->nullable()->after('respondent_name');
            $table->string('email', 120)->nullable()->after('respondent_kelas');
            $table->string('difficulty', 255)->nullable()->after('email');
            $table->unsignedTinyInteger('focus_level')->nullable()->after('difficulty');
            $table->string('bring_journal', 120)->nullable()->after('topic');

            $table->string('topic', 200)->change();
            $table->string('mode', 120)->change();
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'respondent_name',
                'respondent_kelas',
                'email',
                'difficulty',
                'focus_level',
                'bring_journal',
            ]);
        });
    }
};
