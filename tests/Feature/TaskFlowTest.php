<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFlowTest extends TestCase
{
    use RefreshDatabase;

    private function masukSebagaiSiswa(): Student
    {
        $student = Student::create(['name' => 'Rani', 'kelas' => 'Kelas VIII']);

        $this->withSession(['student_id' => $student->id]);

        return $student;
    }

    public function test_siswa_dapat_menyimpan_rencana_tugas_dengan_tiga_langkah(): void
    {
        $student = $this->masukSebagaiSiswa();

        $this->post('/siswa/tugas', [
            'subject' => 'IPA',
            'day' => 'Selasa',
            'priority' => 'Penting & Terjadwal',
            'title' => 'Menyelesaikan laporan pengamatan gaya gesek',
            'time' => 'Pukul 16.00 (25 menit fokus)',
            'subtasks' => ['Siapkan buku', 'Kerjakan nomor 1-5', 'Periksa ulang'],
        ])->assertRedirect();

        $task = Task::where('student_id', $student->id)->firstOrFail();

        $this->assertSame('IPA', $task->subject);
        $this->assertCount(3, $task->subtasks);
    }

    public function test_status_tugas_berubah_saat_dicentang(): void
    {
        $student = $this->masukSebagaiSiswa();

        $task = Task::create([
            'student_id' => $student->id,
            'day' => 'Senin',
            'subject' => 'Matematika',
            'title' => 'Latihan pecahan',
            'priority' => 'Penting & Mendesak',
            'time' => 'Pukul 19.00',
            'completed' => false,
        ]);

        $this->patch("/siswa/tugas/{$task->id}/toggle")->assertRedirect();

        $this->assertTrue($task->fresh()->completed);
    }

    public function test_siswa_tidak_bisa_mengubah_tugas_milik_siswa_lain(): void
    {
        $this->masukSebagaiSiswa();

        $lain = Student::create(['name' => 'Budi', 'kelas' => 'Kelas IX']);

        $task = Task::create([
            'student_id' => $lain->id,
            'day' => 'Rabu',
            'subject' => 'IPS',
            'title' => 'Tugas orang lain',
            'priority' => 'Tambahan / Pengayaan',
            'time' => 'Pukul 20.00',
            'completed' => false,
        ]);

        $this->patch("/siswa/tugas/{$task->id}/toggle")->assertForbidden();
    }

    public function test_siswa_dapat_menghapus_tugasnya_sendiri(): void
    {
        $student = $this->masukSebagaiSiswa();

        $task = Task::create([
            'student_id' => $student->id,
            'day' => 'Kamis',
            'subject' => 'Bahasa Indonesia',
            'title' => 'Menulis puisi',
            'priority' => 'Penting & Terjadwal',
            'time' => 'Pukul 15.00',
            'completed' => false,
        ]);

        $this->delete("/siswa/tugas/{$task->id}")->assertRedirect();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_filter_hari_pada_ceklis_harian(): void
    {
        $student = $this->masukSebagaiSiswa();

        foreach (['Senin', 'Selasa'] as $day) {
            Task::create([
                'student_id' => $student->id,
                'day' => $day,
                'subject' => 'IPA',
                'title' => "Tugas {$day}",
                'priority' => 'Penting & Terjadwal',
                'time' => 'Pukul 16.00',
                'completed' => false,
            ]);
        }

        $this->get('/siswa/ceklis?day=Senin')
            ->assertOk()
            ->assertSee('Tugas Senin')
            ->assertDontSee('Tugas Selasa');
    }

    public function test_ekspor_csv_siswa_memakai_bom_utf8(): void
    {
        $this->masukSebagaiSiswa();

        $response = $this->get('/siswa/ekspor')->assertOk();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $response->streamedContent());
    }
}