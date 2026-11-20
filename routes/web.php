<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Depan (Landing Page) & Portal Masuk (Siswa & Admin BK)
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLanding'])->name('landing');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login.form');

Route::post('/login/siswa', [AuthController::class, 'loginSiswa'])->name('login.siswa');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Login dengan Google (OAuth 2.0)
|--------------------------------------------------------------------------
| Setelah Google mengembalikan identitas pengguna:
|  - email Admin BK  -> langsung masuk Ruang Admin
|  - email lain      -> pop up Nama Panggilan + Kelas di halaman login
*/
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/batal', [GoogleAuthController::class, 'forget'])->name('auth.google.forget');

/*
|--------------------------------------------------------------------------
| Area Siswa (session Nama Panggilan + Kelas)
|--------------------------------------------------------------------------
*/
Route::middleware('siswa')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('index');
    Route::get('/beranda', [SiswaController::class, 'beranda'])->name('beranda');
    Route::get('/perencana', [SiswaController::class, 'perencana'])->name('perencana');
    Route::get('/ceklis', [SiswaController::class, 'ceklis'])->name('ceklis');
    Route::get('/refleksi', [SiswaController::class, 'refleksi'])->name('refleksi');
    Route::get('/materi', [SiswaController::class, 'materi'])->name('materi');
    Route::get('/rpl', [SiswaController::class, 'rpl'])->name('rpl');
    Route::get('/konsultasi', [SiswaController::class, 'konsultasi'])->name('konsultasi');

    // Rencana belajar & pemecah tugas
    Route::post('/tugas', [SiswaController::class, 'storeTask'])->name('tasks.store');
    Route::patch('/tugas/{task}/toggle', [SiswaController::class, 'toggleTask'])->name('tasks.toggle');
    Route::patch('/tugas/{task}/langkah/{subtask}', [SiswaController::class, 'toggleSubtask'])->name('subtasks.toggle');
    Route::delete('/tugas/{task}', [SiswaController::class, 'deleteTask'])->name('tasks.destroy');

    // Refleksi mingguan
    Route::post('/refleksi', [SiswaController::class, 'storeReflection'])->name('reflections.store');

    // Permintaan konsultasi
    Route::post('/konsultasi', [SiswaController::class, 'storeConsultation'])->name('consultations.store');

    // Ekspor tugas milik sendiri ke CSV
    Route::get('/ekspor', [AdminController::class, 'exportSiswaTasks'])->name('export');
});

/*
|--------------------------------------------------------------------------
| Ruang Admin BK (login email + password)
|--------------------------------------------------------------------------
*/
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/konsultasi-masuk', [AdminController::class, 'konsultasiMasuk'])->name('konsultasi-masuk');
    Route::get('/konsultasi-formulir', [AdminController::class, 'editConsultationForm'])->name('konsultasi.form.edit');
    Route::put('/konsultasi-formulir', [AdminController::class, 'updateConsultationForm'])->name('konsultasi.form.update');
    Route::post('/konsultasi-formulir/reset', [AdminController::class, 'resetConsultationForm'])->name('konsultasi.form.reset');
    Route::get('/rpl-admin', [AdminController::class, 'rplAdmin'])->name('rpl-admin');
    Route::get('/integrasi', [AdminController::class, 'integrasi'])->name('integrasi');
    Route::get('/akun-guru', [AdminController::class, 'admins'])->name('admins.index');

    Route::patch('/konsultasi/{consultation}/tandai', [AdminController::class, 'markConsultation'])
        ->name('konsultasi.mark');

    Route::put('/integrasi', [AdminController::class, 'saveLinks'])->name('links.save');

    Route::post('/akun-guru', [AdminController::class, 'storeAdmin'])->name('admins.store');
    Route::delete('/akun-guru/{user}', [AdminController::class, 'destroyAdmin'])->name('admins.destroy');

    Route::get('/ekspor-master', [AdminController::class, 'exportSpreadsheet'])
        ->name('export.master');
});
