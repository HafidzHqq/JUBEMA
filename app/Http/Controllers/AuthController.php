<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Support\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLanding(Request $request): View
    {
        $adminUser = Auth::check() && Auth::user()->isAdmin() ? Auth::user() : null;
        $currentStudent = $adminUser === null ? StudentSession::current($request) : null;

        return view('landing', [
            'adminUser' => $adminUser,
            'currentStudent' => $currentStudent,
            'googleEnabled' => filled(config('services.google.client_id'))
                && filled(config('services.google.client_secret')),
        ]);
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        // Admin yang sudah masuk tetap boleh membuka halaman utama (mode pratinjau).
        // Halaman ini menampilkan jalan pintas kembali ke Ruang Admin, bukan formulir masuk.
        $adminPreview = Auth::check() && Auth::user()->isAdmin();

        if (! $adminPreview && StudentSession::current($request) !== null) {
            return redirect()->route('siswa.beranda');
        }

        // Identitas Google sementara: kalau ada, pop up Nama + Kelas dibuka.
        return view('auth.login', [
            'adminPreview' => $adminPreview,
            'adminUser' => $adminPreview ? Auth::user() : null,
            'pendingGoogle' => (! $adminPreview && $request->session()->get('google_id')) ? [
                'name' => $request->session()->get('google_name'),
                'email' => $request->session()->get('google_email'),
                'avatar' => $request->session()->get('google_avatar'),
            ] : null,
            'googleEnabled' => filled(config('services.google.client_id'))
                && filled(config('services.google.client_secret')),
        ]);
    }

    /**
     * Simpan profil siswa (Nama Panggilan + Kelas) dari pop up setelah login Google.
     */
    public function loginSiswa(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:40'],
            'kelas' => ['required', 'in:Kelas VII,Kelas VIII,Kelas IX'],
            'consent' => ['accepted'],
        ], [
            'name.required' => 'Nama panggilan belum diisi. Isi dulu ya!',
            'name.min' => 'Nama panggilan minimal 2 karakter.',
            'kelas.required' => 'Pilih tingkat kelas SMP kamu dulu.',
            'consent.accepted' => 'Centang persetujuan penggunaan JUBEMA dulu ya.',
        ]);

        $student = Student::findOrCreateByNameAndKelas($data['name'], $data['kelas']);

        // Tautkan akun Google (kalau login lewat Google) supaya pop up
        // Nama + Kelas tidak muncul lagi pada kunjungan berikutnya.
        $student->linkGoogle(
            $request->session()->get('google_id'),
            $request->session()->get('google_email'),
            $request->session()->get('google_avatar')
        );

        StudentSession::put($request, $student);
        Auth::logout();
        $request->session()->forget(GoogleAuthController::PENDING_KEYS);

        return redirect()->route('siswa.beranda')
            ->with('success', "Selamat datang, {$student->name} ({$student->kelas})! Privasi 100% aman.");
    }

    public function logout(Request $request): RedirectResponse
    {
        StudentSession::forget($request);
        $request->session()->forget(GoogleAuthController::PENDING_KEYS);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah keluar dari sesi JUBEMA.');
    }
}
