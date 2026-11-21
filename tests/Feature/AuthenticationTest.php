<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_dapat_dibuka_oleh_tamu(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('JUBEMA')
            ->assertSee('Layanan Bimbingan')
            ->assertSee('Mulai Jurnal Belajar')
            ->assertSee(route('login'), false)
            ->assertDontSee('Saya Guru BK')
            ->assertDontSee('Masuk sebagai Guru BK')
            ->assertDontSee('Untuk Guru BK')
            ->assertDontSee('id="guru-bk"', false)
            ->assertDontSee('role=admin', false);
    }

    public function test_landing_page_menampilkan_pintasan_admin_saat_admin_sudah_masuk(): void
    {
        $admin = User::create([
            'name' => 'Haqiqih',
            'email' => 'admin@jubema.test',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('Kembali ke Ruang Guru BK')
            ->assertSee($admin->email);
    }

    public function test_landing_page_menampilkan_pintasan_siswa_saat_siswa_sudah_masuk(): void
    {
        $student = Student::create(['name' => 'Rani', 'kelas' => 'Kelas VIII']);

        $this->withSession(['student_id' => $student->id])
            ->get('/')
            ->assertOk()
            ->assertSee('Buka Jurnal Belajar')
            ->assertSee('Rani');
    }

    public function test_halaman_login_dapat_dibuka(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('JUBEMA')
            ->assertSee('Masuk dengan Google');
    }

    public function test_pop_up_nama_dan_kelas_muncul_setelah_login_google(): void
    {
        $this->withSession([
            'google_id' => '1234567890',
            'google_name' => 'Rani Putri',
            'google_email' => 'rani@gmail.com',
            'google_avatar' => null,
        ])->get('/login')
            ->assertOk()
            ->assertSee('id="profile-modal"', false)
            ->assertSee('Satu langkah lagi');
    }

    public function test_pop_up_tidak_muncul_tanpa_identitas_google(): void
    {
        $this->get('/login')->assertOk()->assertDontSee('id="profile-modal"', false);
    }

    public function test_siswa_melengkapi_profil_dari_pop_up(): void
    {
        $response = $this->withSession([
            'google_id' => '1234567890',
            'google_name' => 'Rani Putri',
            'google_email' => 'rani@gmail.com',
        ])->post('/login/siswa', [
            'name' => 'Rani',
            'kelas' => 'Kelas VIII',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('siswa.beranda'));

        $this->assertDatabaseHas('students', [
            'name' => 'Rani',
            'kelas' => 'Kelas VIII',
            'google_id' => '1234567890',
            'google_email' => 'rani@gmail.com',
        ]);

        $this->get('/siswa/beranda')->assertOk()->assertSee('Rani');
    }

    public function test_siswa_tanpa_persetujuan_ditolak(): void
    {
        $this->post('/login/siswa', [
            'name' => 'Rani',
            'kelas' => 'Kelas VIII',
        ])->assertSessionHasErrors('consent');
    }

    public function test_siswa_tidak_bisa_membuka_area_admin(): void
    {
        Student::create(['name' => 'Rani', 'kelas' => 'Kelas VIII']);

        $this->withSession(['student_id' => 1])
            ->get('/admin')
            ->assertRedirect(route('login'));
    }

    public function test_admin_bisa_membuka_halaman_login_tanpa_dialihkan(): void
    {
        $admin = User::create([
            'name' => 'Haqiqih',
            'email' => 'admin@jubema.test',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/login')
            ->assertOk()
            ->assertSee('Halaman Utama JUBEMA')
            ->assertSee('Kembali ke Ruang Guru BK')
            ->assertSee($admin->email)
            ->assertDontSee('id="panel-login"', false);
    }

    public function test_tamu_hanya_melihat_login_google_tanpa_opsi_masuk_guru(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('id="panel-login"', false)
            ->assertSee('Masuk dengan Google')
            ->assertDontSee('id="form-admin"', false)
            ->assertDontSee('id="tab-admin"', false)
            ->assertDontSee('Halaman Utama JUBEMA');
    }

    public function test_rute_login_admin_manual_tidak_tersedia(): void
    {
        $this->post('/login/admin', [
            'email' => 'admin@jubema.test',
            'password' => 'rahasia123',
        ])->assertNotFound();

        $this->assertGuest();
    }

    public function test_logout_membersihkan_sesi_siswa_dan_admin(): void
    {
        $this->withSession(['student_id' => 1])
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_dapat_menambah_dan_menghapus_akun_guru_bk_lain(): void
    {
        $admin = User::create([
            'name' => 'Hesni Maharani',
            'email' => 'hesnimaharani31@gmail.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/akun-guru')
            ->assertOk()
            ->assertSee('Kelola akun');

        $this->actingAs($admin)
            ->post('/admin/akun-guru', [
                'name' => 'Ibu Siti',
                'email' => 'Siti.BK@gmail.com',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Ibu Siti',
            'email' => 'siti.bk@gmail.com',
            'role' => 'admin',
        ]);

        $baru = User::where('email', 'siti.bk@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->delete("/admin/akun-guru/{$baru->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $baru->id]);

        // Tidak boleh keluar/menghapus akun sendiri jika merupakan satu-satunya admin
        $this->actingAs($admin)
            ->delete("/admin/akun-guru/{$admin->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_utama_dapat_keluar_dari_admin_jika_ada_admin_lain_aktif(): void
    {
        $utama = User::create([
            'name' => 'Hesni Maharani',
            'email' => 'hesnimaharani31@gmail.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);

        $rekan = User::create([
            'name' => 'Ibu Siti',
            'email' => 'siti.bk@gmail.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
        ]);

        $this->actingAs($utama)
            ->delete("/admin/akun-guru/{$utama->id}")
            ->assertRedirect(route('login'))
            ->assertSessionHas('info');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $utama->id]);
        $this->assertDatabaseHas('users', ['id' => $rekan->id, 'role' => 'admin']);
    }
}