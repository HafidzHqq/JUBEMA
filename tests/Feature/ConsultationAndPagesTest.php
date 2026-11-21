<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConsultationAndPagesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Ibu Rani',
            'email' => 'gurubk@jubema.test',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);
    }

    private function createStudent(): Student
    {
        return Student::create([
            'name' => 'Rani',
            'kelas' => 'Kelas VIII',
            'google_id' => 'g-rani-1',
            'google_email' => 'rani@gmail.com',
        ]);
    }

    public function test_semua_halaman_siswa_dapat_dibuka_tanpa_error(): void
    {
        $student = $this->createStudent();

        foreach ([
            '/siswa/beranda',
            '/siswa/perencana',
            '/siswa/ceklis',
            '/siswa/refleksi',
            '/siswa/materi',
            '/siswa/rpl',
            '/siswa/konsultasi',
        ] as $url) {
            $this->withSession(['student_id' => $student->id])
                ->get($url)
                ->assertOk();
        }
    }

    public function test_semua_halaman_admin_dapat_dibuka_tanpa_error(): void
    {
        $admin = $this->createAdmin();

        foreach ([
            '/admin',
            '/admin/konsultasi-masuk',
            '/admin/konsultasi-formulir',
            '/admin/rpl-admin',
            '/admin/integrasi',
            '/admin/akun-guru',
        ] as $url) {
            $this->actingAs($admin)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_admin_dapat_menambah_dan_menghapus_pertanyaan_formulir_konsultasi(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        // Admin mengganti formulir menjadi 2 pertanyaan kustom (tambah + hapus pertanyaan bawaan)
        $this->actingAs($admin)
            ->put('/admin/konsultasi-formulir', [
                'intro' => 'Pengantar konsultasi khusus semester ini.',
                'questions' => [
                    [
                        'id' => 'q_mapel',
                        'label' => 'Mata pelajaran apa yang paling sulit minggu ini?',
                        'type' => 'text',
                        'required' => '1',
                        'placeholder' => 'Misal: Matematika',
                    ],
                    [
                        'id' => 'q_waktu',
                        'label' => 'Pilih sesi konsultasi yang diinginkan',
                        'type' => 'radio',
                        'required' => '1',
                        'options' => ['Istirahat 1', 'Sepulang sekolah'],
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        // Halaman siswa langsung menampilkan pertanyaan baru
        $this->withSession(['student_id' => $student->id])
            ->get('/siswa/konsultasi')
            ->assertOk()
            ->assertSee('Mata pelajaran apa yang paling sulit minggu ini?')
            ->assertSee('Istirahat 1');

        // Siswa mengirim formulir dengan pertanyaan kustom tersebut
        $this->withSession(['student_id' => $student->id])
            ->post('/siswa/konsultasi', [
                'record_email' => '1',
                'answers' => [
                    'q_mapel' => 'Matematika Pecahan',
                    'q_waktu' => 'Istirahat 1',
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $consultation = Consultation::firstOrFail();
        $this->assertSame('Matematika Pecahan', $consultation->displayTopic());
        $this->assertCount(2, $consultation->formattedAnswers());

        // Halaman admin menampilkan jawaban kustom
        $this->actingAs($admin)
            ->get('/admin/konsultasi-masuk')
            ->assertOk()
            ->assertSee('Matematika Pecahan')
            ->assertSee('Istirahat 1');
    }

    public function test_admin_dapat_menjadwalkan_waktu_dan_tempat_serta_membatalkan_konsultasi(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $consultation = Consultation::create([
            'student_id' => $student->id,
            'respondent_name' => $student->name,
            'respondent_kelas' => 'VIII',
            'topic' => 'Mengatur jadwal harian',
            'mode' => 'Jam istirahat sekolah',
            'consultation_method' => 'Konsultasi tatap muka di Ruang BK',
            'status' => 'terkirim',
        ]);

        // Tetapkan tanggal, jam, tempat, dan catatan
        $this->actingAs($admin)
            ->patch("/admin/konsultasi/{$consultation->id}/tandai", [
                'scheduled_date' => '2026-11-25',
                'scheduled_time' => '10:15',
                'scheduled_location' => 'Ruang BK Lantai 1',
                'scheduled_note' => 'Bawa buku catatan mingguan ya.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $consultation->refresh();
        $this->assertSame('sudah_dijadwalkan', $consultation->status);
        $this->assertSame('2026-11-25 10:15', $consultation->scheduled_at->format('Y-m-d H:i'));
        $this->assertSame('Ruang BK Lantai 1', $consultation->scheduled_location);
        $this->assertSame('Bawa buku catatan mingguan ya.', $consultation->scheduled_note);

        // Siswa langsung melihat waktu & tempat jadwal di riwayat konsultasinya
        $this->withSession(['student_id' => $student->id])
            ->get('/siswa/konsultasi')
            ->assertOk()
            ->assertSee('Ruang BK Lantai 1')
            ->assertSee('10:15 WIB')
            ->assertSee('Bawa buku catatan mingguan ya.');

        // Batalkan jadwal
        $this->actingAs($admin)
            ->patch("/admin/konsultasi/{$consultation->id}/tandai", [
                'action' => 'cancel',
            ])
            ->assertRedirect()
            ->assertSessionHas('info');

        $consultation->refresh();
        $this->assertSame('menunggu_jadwal_bk', $consultation->status);
        $this->assertNull($consultation->scheduled_at);
        $this->assertNull($consultation->scheduled_location);
        $this->assertNull($consultation->scheduled_note);
    }
}
