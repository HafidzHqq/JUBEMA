<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Support\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Login JUBEMA dengan akun Google.
 *
 * Alur:
 *  1. Siswa/Admin menekan "Masuk dengan Google".
 *  2. Google mengembalikan email + identitas ke /auth/google/callback.
 *  3. Email yang terdaftar sebagai Admin BK  -> masuk Ruang Admin.
 *     Email lain (siswa)                    -> pop up Nama Panggilan + Kelas.
 */
class GoogleAuthController extends Controller
{
    /** Kunci session sementara selama proses pemilihan profil siswa. */
    public const PENDING_KEYS = [
        'google_id',
        'google_name',
        'google_email',
        'google_avatar',
    ];

    /**
     * Kirim pengguna ke halaman persetujuan Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Terima balasan dari Google lalu tentukan peran pengguna.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();
        $nama = trim((string) $googleUser->getName()) ?: Str::before($email, '@');
        $avatar = $googleUser->getAvatar();

        // --- 1) Admin BK -------------------------------------------------
        if ($this->isAdminEmail($email)) {
            $user = User::firstOrNew(['email' => $email]);

            $user->name = $user->name ?: $nama;
            $user->role = 'admin';

            if (! $user->exists || blank($user->password)) {
                $user->password = Str::random(40); // akun Google tidak perlu sandi
            }

            $user->forceFill([
                'google_id' => $googleId,
                'avatar_url' => $avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            Auth::login($user, true);
            StudentSession::forget($request);
            $request->session()->forget(self::PENDING_KEYS);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', "Selamat datang, {$user->name}. Anda masuk sebagai Guru BK.");
        }

        // --- 2) Siswa yang akun Google-nya sudah pernah terdaftar --------
        $student = Student::where('google_id', $googleId)->first();

        if ($student !== null) {
            Auth::logout();
            StudentSession::put($request, $student);
            $request->session()->forget(self::PENDING_KEYS);

            return redirect()->route('siswa.beranda')
                ->with('success', "Selamat datang kembali, {$student->name} ({$student->kelas})!");
        }

        // --- 3) Siswa baru: tampilkan pop up Nama Panggilan + Kelas ------
        $request->session()->put([
            'google_id' => $googleId,
            'google_name' => $nama,
            'google_email' => $email,
            'google_avatar' => $avatar,
        ]);

        return redirect()->route('login')
            ->with('info', 'Satu langkah lagi: lengkapi nama panggilan dan kelasmu.');
    }

    /**
     * Batalkan identitas Google sementara (mis. salah pilih akun).
     */
    public function forget(Request $request): RedirectResponse
    {
        $request->session()->forget(self::PENDING_KEYS);

        return redirect()->route('login')
            ->with('info', 'Akun Google dibatalkan. Silakan masuk dengan akun lain.');
    }

    /**
     * Apakah email Google ini berhak atas Ruang Admin BK?
     */
    private function isAdminEmail(string $email): bool
    {
        if ($email === '') {
            return false;
        }

        $allowlist = array_filter(array_merge(
            [(string) config('app.jubema.admin_email')],
            (array) config('app.jubema.admin_emails', [])
        ));

        foreach ($allowlist as $candidate) {
            if (Str::lower(trim((string) $candidate)) === $email) {
                return true;
            }
        }

        // Email yang sudah terdaftar sebagai admin di tabel `users` juga boleh masuk.
        return User::whereRaw('LOWER(email) = ?', [$email])
            ->where('role', 'admin')
            ->exists();
    }
}