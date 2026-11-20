<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Reflection;
use App\Models\Student;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * Data contoh agar dashboard Admin BK langsung terisi.
 * Mengikuti contoh pada JUBEMA_Spreadsheet_Rani.csv.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $student = Student::firstOrCreate(
            ['name' => 'Rani (Contoh)', 'kelas' => 'Kelas VIII'],
        );

        // Hanya buat contoh sekali.
        if ($student->tasks()->exists()) {
            return;
        }

        $task = Task::create([
            'student_id' => $student->id,
            'day' => 'Selasa',
            'subject' => 'IPA',
            'title' => 'Menyelesaikan laporan pengamatan gaya gesek',
            'priority' => 'Penting & Terjadwal',
            'time' => 'Pukul 16.00 (25 menit fokus)',
            'completed' => false,
        ]);

        foreach ([
            'Siapkan buku catatan & hasil pengamatan',
            'Tulis bagian tujuan dan alat bahan',
            'Rapikan kesimpulan dan kumpulkan',
        ] as $i => $text) {
            Subtask::create([
                'task_id' => $task->id,
                'text' => $text,
                'done' => $i === 0,
                'position' => $i,
            ]);
        }

        Reflection::create([
            'student_id' => $student->id,
            'week' => 'Minggu ke-1 November 2026',
            'mood' => 'Cukup Terbantu',
            'good' => 'Berhasil menyelesaikan PR Matematika lebih cepat karena memakai waktu fokus 25 menit.',
            'obstacle' => 'Masih sering membuka ponsel saat sedang membaca materi IPA.',
            'plan' => 'Menaruh ponsel di luar meja belajar dan memakai pengatur waktu Pomodoro.',
            'shared_with_bk' => true,
        ]);

        Consultation::create([
            'student_id' => $student->id,
            'topic' => 'Sulit fokus belajar di rumah pada malam hari',
            'mode' => 'Konsultasi tatap muka saat jam istirahat',
            'message' => 'Saya merasa cepat mengantuk ketika belajar malam. Apakah lebih baik belajar sore setelah makan?',
            'status' => 'terkirim',
        ]);

        $this->command?->info('Data contoh siswa "Rani (Contoh)" tersimpan.');
    }
}
