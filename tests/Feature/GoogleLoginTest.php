<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

/**
 * Alur "Masuk dengan Google".
 *
 * Google tidak benar-benar dihubungi: driver Socialite diganti dengan
 * objek tiruan (mock) supaya alur callback bisa diuji secara lokal.
 */
class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(string $id, string $email, string $name): void
    {
        $user = (new SocialiteUser)->map([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'avatar' => 'https://example.test/avatar.png',
        ]);

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('user')->andReturn($user);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_tombol_google_mengarahkan_ke_halaman_persetujuan_google(): void
    {
        $this->get('/auth/google')
            ->assertRedirectContains('accounts.google.com');
    }

    public function test_email_admin_google_langsung_masuk_ruang_admin(): void
    {
        $this->fakeGoogleUser('g-admin-1', 'hesnimaharani31@gmail.com', 'Hesni Maharani');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'hesnimaharani31@gmail.com',
            'google_id' => 'g-admin-1',
            'role' => 'admin',
        ]);

        $this->get('/admin')->assertOk();
    }

    public function test_email_bukan_admin_mendapat_pop_up_nama_dan_kelas(): void
    {
        $this->fakeGoogleUser('g-siswa-1', 'rani@gmail.com', 'Rani Putri');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHas('google_id', 'g-siswa-1');

        $this->assertGuest();
    }

    public function test_siswa_dengan_google_id_terdaftar_langsung_masuk_beranda(): void
    {
        $student = Student::create([
            'name' => 'Rani',
            'kelas' => 'Kelas VIII',
            'google_id' => 'g-siswa-1',
            'google_email' => 'rani@gmail.com',
        ]);

        $this->fakeGoogleUser('g-siswa-1', 'rani@gmail.com', 'Rani Putri');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('siswa.beranda'))
            ->assertSessionHas('student_id', $student->id);
    }

    public function test_membatalkan_akun_google_menghapus_identitas_sementara(): void
    {
        $this->withSession(['google_id' => 'g-siswa-1', 'google_email' => 'rani@gmail.com'])
            ->get('/auth/google/batal')
            ->assertRedirect(route('login'))
            ->assertSessionMissing('google_id');
    }
}