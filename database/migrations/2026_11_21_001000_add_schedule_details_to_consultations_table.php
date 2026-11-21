<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('scheduled_location', 150)->nullable()->after('scheduled_at');
            $table->string('scheduled_note', 300)->nullable()->after('scheduled_location');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['scheduled_location', 'scheduled_note']);
        });
    }
};
