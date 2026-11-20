<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelBehaviourTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_hari_dan_prioritas_tersedia(): void
    {
        $this->assertCount(7, Task::DAYS);
        $this->assertSame('Senin', Task::DAYS[0]);
        $this->assertSame('Minggu', Task::DAYS[6]);
        $this->assertCount(3, Task::PRIORITIES);
    }

    public function test_setting_get_dan_put(): void
    {
        Setting::put('google_form_url', 'https://forms.gle/contoh');

        $this->assertSame('https://forms.gle/contoh', Setting::get('google_form_url'));
        $this->assertSame('cadangan', Setting::get('tidak_ada', 'cadangan'));

        Setting::put('google_form_url', 'https://forms.gle/baru');
        $this->assertSame('https://forms.gle/baru', Setting::get('google_form_url'));
    }

    public function test_tugas_otomatis_tuntas_bila_semua_langkah_selesai(): void
    {
        $task = Task::create([
            'student_id' => \App\Models\Student::create(['name' => 'Rani', 'kelas' => 'Kelas VIII'])->id,
            'day' => 'Senin',
            'subject' => 'IPA',
            'title' => 'Latihan',
            'priority' => 'Penting & Terjadwal',
            'time' => 'Pukul 16.00',
            'completed' => false,
        ]);

        Subtask::create(['task_id' => $task->id, 'text' => 'Langkah 1', 'done' => false, 'position' => 0]);
        Subtask::create(['task_id' => $task->id, 'text' => 'Langkah 2', 'done' => false, 'position' => 1]);

        $task->subtasks()->update(['done' => false]);
        $task->syncCompletionFromSubtasks();
        $this->assertFalse($task->fresh()->completed);

        $task->subtasks()->update(['done' => true]);
        $task->syncCompletionFromSubtasks();
        $this->assertTrue($task->fresh()->completed);
    }
}