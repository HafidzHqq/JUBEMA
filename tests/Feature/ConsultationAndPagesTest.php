<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Material;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_dapat_mengunggah_dan_menghapus_materi_pdf_dan_tautan(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $pdf = UploadedFile::fake()->create('panduan-fokus.pdf', 120, 'application/pdf');

        // Unggah file PDF
        $this->actingAs($admin)
            ->post('/admin/materi', [
                'title' => 'Panduan Fokus 25 Menit',
                'meta' => 'Bacaan 5 menit · Fokus',
                'summary' => 'Langkah praktis memulai belajar 25 menit.',
                'source_type' => 'file',
                'material_file' => $pdf,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $material = Material::where('title', 'Panduan Fokus 25 Menit')->firstOrFail();
        $this->assertSame('file', $material->source_type);
        Storage::disk('public')->assertExists($material->file_path);

        // Siswa melihat materi di halaman Materi dan bisa mengakses berkas PDF-nya
        $this->withSession(['student_id' => $student->id])
            ->get('/siswa/materi')
            ->assertOk()
            ->assertSee('Panduan Fokus 25 Menit');

        $this->withSession(['student_id' => $student->id])
            ->get(route('materi.file', $material))
            ->assertOk();

        // Admin menghapus materi -> file ikut terhapus
        $filePath = $material->file_path;
        $this->actingAs($admin)
            ->delete(route('admin.materials.destroy', $material))
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_siswa_dan_admin_dapat_menghapus_refleksi_dan_konsultasi(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $otherStudent = Student::create(['name' => 'Budi', 'kelas' => 'Kelas IX']);

        $reflection = $student->reflections()->create([
            'week' => 'Minggu ke-2',
            'mood' => 'Lega & Bangga',
            'good' => 'Tepat waktu',
            'obstacle' => 'HP',
            'plan' => 'Silent HP',
            'shared_with_bk' => true,
        ]);

        // Siswa lain tidak boleh menghapus refleksi siswa ini
        $this->withSession(['student_id' => $otherStudent->id])
            ->delete(route('siswa.reflections.destroy', $reflection))
            ->assertForbidden();

        // Pemilik boleh menghapus refleksinya sendiri
        $this->withSession(['student_id' => $student->id])
            ->delete(route('siswa.reflections.destroy', $reflection))
            ->assertRedirect();
        $this->assertDatabaseMissing('reflections', ['id' => $reflection->id]);

        $c1 = Consultation::create([
            'student_id' => $student->id,
            'respondent_name' => $student->name,
            'respondent_kelas' => 'VIII',
            'topic' => 'Konsultasi 1',
            'mode' => 'Istirahat',
            'status' => 'terkirim',
        ]);

        $c2 = Consultation::create([
            'student_id' => $student->id,
            'respondent_name' => $student->name,
            'respondent_kelas' => 'VIII',
            'topic' => 'Konsultasi 2',
            'mode' => 'Pulang sekolah',
            'status' => 'terkirim',
        ]);

        // Siswa lain tidak boleh menghapus konsultasi siswa ini
        $this->withSession(['student_id' => $otherStudent->id])
            ->delete(route('siswa.consultations.destroy', $c1))
            ->assertForbidden();

        // Siswa menghapus konsultasinya sendiri
        $this->withSession(['student_id' => $student->id])
            ->delete(route('siswa.consultations.destroy', $c1))
            ->assertRedirect();
        $this->assertDatabaseMissing('consultations', ['id' => $c1->id]);

        // Admin menghapus konsultasi masuk
        $this->actingAs($admin)
            ->delete(route('admin.konsultasi.destroy', $c2))
            ->assertRedirect();
        $this->assertDatabaseMissing('consultations', ['id' => $c2->id]);
    }

    public function test_ekspor_rekap_kelas_admin_berformat_excel_xlsx_multi_sheet(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $student->tasks()->create([
            'day' => 'Senin',
            'subject' => 'Matematika',
            'title' => 'Latihan Aljabar',
            'priority' => 'Penting & Mendesak',
            'time' => '16.00',
            'completed' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.export.master'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $file = $response->getFile();
        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($file->getPathname()) === true);
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $sheet1Xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        $this->assertStringContainsString('Rencana Belajar', (string) $workbookXml);
        $this->assertStringContainsString('Refleksi Siswa', (string) $workbookXml);
        $this->assertStringContainsString('Konsultasi BK', (string) $workbookXml);
        $this->assertStringContainsString('Latihan Aljabar', (string) $sheet1Xml);
    }
}
