<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('meta', 100)->nullable();
            $table->string('summary', 500)->nullable();
            $table->enum('source_type', ['file', 'link'])->default('file');
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 200)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('materials')->insert([
            [
                'title' => 'Tiga Siklus Belajar Mandiri (Zimmerman)',
                'meta' => 'Bacaan 3 menit · Regulasi Diri',
                'summary' => 'Belajar efektif tidak menuntut kesempurnaan, melainkan berputar dalam tiga tahap: perencanaan awal, pelaksanaan terfokus, dan refleksi jujur di akhir minggu.',
                'source_type' => 'link',
                'file_path' => null,
                'file_name' => null,
                'external_url' => 'https://docs.google.com/document/d/1YU04gttpATKqHqN44Z-qEWDjFXxY7Xke/edit',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Mengatasi Kebiasaan Menunda Tugas',
                'meta' => 'Bacaan 4 menit · Memulai Tugas',
                'summary' => 'Gunakan aturan lima menit: cukup mulai bagian paling ringan selama lima menit pertama agar otak tidak lagi melihat tugas sebagai beban besar.',
                'source_type' => 'link',
                'file_path' => null,
                'file_name' => null,
                'external_url' => 'https://docs.google.com/document/d/1ACNAUQSvz0i_VgzbSy8QqzEmWLOIGt92/edit',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
