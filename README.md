# JUBEMA — Laravel 11 + MySQL

**Jurnal Digital Manajemen Waktu Belajar** (Fase D SMP Kelas VII–IX) — Layanan Bimbingan dan Konseling.

Versi ini adalah hasil migrasi dari aplikasi satu-file `index.html` menjadi aplikasi Laravel 11
dengan database MySQL, tanpa mengubah tampilan, alur, maupun bahasa yang sudah ada.

---

## 1. Ringkasan Arsitektur

| Aspek | Keterangan |
|---|---|
| Framework | Laravel 11 (PHP ^8.2) |
| Database | MySQL / MariaDB — nama database `jubema` |
| Templating | Blade |
| CSS | Tailwind CSS via CDN (tanpa build step, sama seperti versi HTML) |
| Ikon & Font | Font Awesome 6.4, Google Fonts (Inter + Plus Jakarta Sans) |
| Autentikasi | Google OAuth 2.0 via `laravel/socialite` |
| Session / Cache / Queue | driver `database` |

### Dua sistem identitas (penting)

| Peran | Tabel | Login | Middleware |
|---|---|---|---|
| **Siswa** | `students` | **Masuk dengan Google** → pop up **Nama Panggilan + Kelas** (tanpa kata sandi, tanpa NISN) | `siswa` |
| **Admin BK** | `users` | **Masuk dengan Google** (email terdaftar) *atau* Email + kata sandi | `admin` |

Sesi siswa disimpan pada key `student_id` melalui helper `App\Support\StudentSession`.
Desain ini menjaga privasi siswa 100%: tidak ada NISN, tidak ada kata sandi yang perlu diingat siswa.

### Alur login (Google OAuth)

```
Klik "Masuk dengan Google"
        │
        ▼
Google OAuth consent  ──►  /auth/google/callback
        │
        ├─ email = admin (allowlist) ──────► Auth::login  ──►  /admin           (Ruang Admin BK)
        │
        ├─ google_id sudah terdaftar ──────► StudentSession ──► /siswa/beranda   (langsung masuk)
        │
        └─ siswa baru ─────────────────────► pop up Nama Panggilan + Kelas
                                                    │
                                                    ▼
                                            POST /login/siswa  ──►  /siswa/beranda
```

Email admin ditentukan oleh `JUBEMA_ADMIN_EMAIL` + daftar tambahan `JUBEMA_ADMIN_EMAILS`,
serta **semua email yang sudah ada di tabel `users`** (dibandingkan case-insensitive).

---

## 2. Kebutuhan Sistem

- PHP **8.2** atau lebih baru (`php -v`)
- Composer (`composer -V`)
- MySQL 8 / MariaDB 10.4 atau lebih baru
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`
- **Kredensial Google OAuth** (Client ID + Client Secret) — lihat bagian 3.5

> Folder `vendor/` belum disertakan di repositori. Jalankan `composer install` sekali
> sebelum aplikasi bisa dipakai.

---

## 3. Langkah Instalasi

### 3.1 Masuk ke folder project

```bash
cd laravel
```

### 3.2 Install dependency

```bash
composer install
```

### 3.3 Buat file environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3.4 Buat database MySQL

```bash
mysql -u root -p -e "CREATE DATABASE jubema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Sesuaikan kredensial pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jubema
DB_USERNAME=jubema
DB_PASSWORD=kata_sandi_mysql_anda
```

### 3.5 Konfigurasi Google OAuth (wajib untuk login)

Login JUBEMA memakai Google OAuth 2.0, jadi butuh kredensial dari Google Cloud Console.

**Langkah di <https://console.cloud.google.com/>:**

1. Buat project (contoh: `jubema`).
2. **APIs & Services → OAuth consent screen** — pilih *External*, isi nama aplikasi,
support email, dan tambahkan scope `.../auth/userinfo.email` + `.../auth/userinfo.profile`.
Tambahkan akun Google yang akan dipakai sebagai **Test user** selama status masih *Testing*.
3. **APIs & Services → Credentials → Create credentials → OAuth client ID** → tipe **Web application**.
4. Isi **Authorized JavaScript origins**:

   ```
   http://localhost:8000
   ```

5. Isi **Authorized redirect URIs**:

   ```
   http://localhost:8000/auth/google/callback
   ```

6. Salin **Client ID** dan **Client secret** ke `.env`:

```env
GOOGLE_CLIENT_ID=xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

> Untuk domain produksi, tambahkan juga origin dan redirect URI produksi
> (`https://domain-anda.sch.id` dan `https://domain-anda.sch.id/auth/google/callback`),
> lalu sesuaikan `GOOGLE_REDIRECT_URI`.
>
> Bila kredensial kosong, halaman login menampilkan peringatan kuning dan tombol Google
> tidak dapat dipakai.

### 3.6 Migrasi + isi data awal (seeder)

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel (7 tabel JUBEMA + 8 tabel bawaan Laravel) dan mengisi:

- **Akun Admin BK: `haqiqihf@gmail.com` / `Rani010806`**
- Tautan Google Workspace default (Form, Spreadsheet, Drive)
- Contoh data siswa **"Rani (Contoh)"** Kelas VIII (1 tugas + 3 langkah kecil, 1 refleksi, 1 konsultasi)

Jika ingin mengulang dari nol: `php artisan migrate:fresh --seed`

### 3.7 Jalankan

```bash
php artisan serve
```

Buka <http://localhost:8000>.

> Pastikan port `8000` sesuai dengan `GOOGLE_REDIRECT_URI` dan daftar *Authorized redirect URIs*
> di Google Cloud Console. Bila memakai port lain (`php artisan serve --port=8080`),
> ubah kedua tempat tersebut sekaligus.

---

## 4. Menggunakan Aplikasi

### Login siswa (Google → pop up)
1. Tab **Masuk Siswa** → klik **Masuk dengan Google** → pilih akun Google.
2. **Pop up** otomatis muncul: Nama Panggilan (sudah terisi dari nama akun Google, bisa diubah)
   dan pilihan **Kelas VII / VIII / IX**, plus centang persetujuan.
3. Klik **Mulai Jurnal Belajar** → siswa masuk ke Beranda dan bisa memakai 7 menu alur belajar.

Siswa yang sudah pernah menyelesaikan pop up akan **langsung masuk** pada login Google berikutnya
(karena `students.google_id` sudah tersimpan).

> Akun Google siswa **tidak** dipakai sebagai identitas akademik — hanya sebagai kunci masuk.
> Guru BK tetap melihat Nama Panggilan + Kelas, bukan email siswa.
> Bila ingin berganti akun Google, klik **"Bukan akun saya? Ganti akun Google"** di pop up.

### Login Admin BK
1. Tab **Ruang Admin BK**, lalu pilih salah satu:
   - **Masuk dengan Google** memakai `haqiqihf@gmail.com` (paling praktis, tanpa kata sandi), atau
   - isi email `haqiqihf@gmail.com` + sandi `Rani010806` pada formulir manual.
2. Tersedia 4 menu: Dashboard & Spreadsheet, Permintaan Konsultasi, Dokumen RPL, Pengaturan Tautan.

> Email admin lain bisa ditambahkan lewat `JUBEMA_ADMIN_EMAILS=email1@sekolah.sch.id,email2@sekolah.sch.id`
> (pisahkan dengan koma). Semua email tersebut langsung masuk ke Ruang Admin saat login Google.

> **Admin membuka `/` tanpa dialihkan.** Bila admin yang sudah masuk membuka `/` secara langsung
> (tautan tidak ada di navbar), tampil panel pratinjau "Halaman Utama JUBEMA" berisi tombol
> **Kembali ke Ruang Admin BK** dan **Keluar dari akun ini** — bukan formulir login.
> Tamu tetap melihat formulir login biasa.

### Menu siswa
| No | Menu | Rute |
|---|---|---|
| 1 | Beranda & Alur | `GET /siswa/beranda` |
| 2 | Perencana & Pemecah Tugas | `GET /siswa/perencana` |
| 3 | Ceklis Harian & Fokus (Pomodoro 25 menit) | `GET /siswa/ceklis` |
| 4 | Refleksi Mingguan | `GET /siswa/refleksi` |
| — | Materi & Google Drive | `GET /siswa/materi` |
| — | RPL Layanan BK (siap cetak PDF) | `GET /siswa/rpl` |
| 5 | Ruang Konsultasi (Google Form) | `GET /siswa/konsultasi` |

### Menu Admin BK
| Menu | Rute |
|---|---|
| Dashboard & Spreadsheet Excel | `GET /admin` |
| Permintaan Konsultasi & Google Form | `GET /admin/konsultasi-masuk` |
| Dokumen RPL & Google Drive | `GET /admin/rpl-admin` |
| Pengaturan Tautan Google Workspace | `GET /admin/integrasi` |

---

## 5. Ekspor ke Excel / Google Sheets

| Tombol | Rute | Isi |
|---|---|---|
| Sinkron / Ekspor Spreadsheet (siswa) | `GET /siswa/ekspor` | Tugas milik siswa yang sedang login |
| Ekspor Master Spreadsheet (admin) | `GET /admin/ekspor-master` | Semua tugas, refleksi, dan konsultasi |

File CSV memakai BOM UTF-8 (`\xEF\xBB\xBF`) agar karakter Indonesia terbaca rapi di Microsoft Excel.
Impor file tersebut ke Google Sheets melalui **File → Import → Upload**.

---

## 6. Mengubah Kredensial & Tautan Google

Semuanya melalui `.env`, lalu jalankan ulang seeder bila perlu:

```env
JUBEMA_ADMIN_NAME="Haqiqih"
JUBEMA_ADMIN_EMAIL=haqiqihf@gmail.com
JUBEMA_ADMIN_PASSWORD=Rani010806

# Email admin tambahan (opsional, pisahkan dengan koma)
JUBEMA_ADMIN_EMAILS=

JUBEMA_GFORM_URL=https://forms.gle/Qg653vMCXYTVmg1e9
JUBEMA_GSHEET_URL=https://docs.google.com/spreadsheets
JUBEMA_GDRIVE_URL=https://drive.google.com
```

Tautan juga bisa diubah langsung dari aplikasi pada menu
**Admin → Pengaturan Tautan Google Workspace** (tersimpan ke tabel `settings` dan menimpa nilai `.env`).

---

## 7. Struktur Penting

```
laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php      # form login, pop up profil siswa, login admin, logout
│   │   ├── GoogleAuthController.php# redirect & callback Google OAuth, batalkan akun
│   │   ├── SiswaController.php     # 7 halaman siswa + CRUD tugas/refleksi/konsultasi
│   │   └── AdminController.php     # 4 halaman admin + ekspor CSV
│   ├── Http/Middleware/
│   │   ├── EnsureUserIsSiswa.php   # penjaga area siswa
│   │   └── EnsureUserIsAdmin.php   # penjaga area Admin BK
│   ├── Models/                     # User, Student, Task, Subtask, Reflection, Consultation, Setting
│   ├── Providers/AppServiceProvider.php  # share $workspaceLinks ke semua view
│   └── Support/StudentSession.php  # sesi siswa (key: student_id)
├── database/
│   ├── migrations/                 # 15 tabel (7 JUBEMA + 8 bawaan Laravel)
│   └── seeders/                    # AdminUserSeeder, SettingSeeder, DemoDataSeeder
├── resources/views/
│   ├── layouts/app.blade.php       # header, navigasi, footer, toast, print CSS
│   ├── auth/login.blade.php        # halaman masuk + pop up Nama Panggilan & Kelas
│   ├── siswa/                      # beranda, perencana, ceklis, refleksi, materi, rpl, konsultasi
│   └── admin/                      # dashboard, konsultasi-masuk, rpl-admin, integrasi
└── routes/web.php
```

### Skema database

| Tabel | Kolom inti |
|---|---|
| `users` | name, email (unik), password, role (`admin`), google_id, avatar_url |
| `students` | name, kelas — unik gabungan (name, kelas), google_id (unik), google_email, google_avatar |
| `tasks` | student_id, day, subject, title, priority, time, completed |
| `subtasks` | task_id, text, done, position |
| `reflections` | student_id, week, mood, good, obstacle, plan, shared_with_bk |
| `consultations` | student_id, topic, mode, message, status, scheduled_at |
| `settings` | key (unik), value |
| `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `migrations` | bawaan Laravel (driver database) |

---

## 8. Pemecahan Masalah

| Gejala | Penyebab & solusi |
|---|---|
| `could not find driver` | Ekstensi `pdo_mysql` belum aktif. Aktifkan di `php.ini`. |
| `Base table or view not found` | Belum menjalankan `php artisan migrate --seed`. |
| `No application encryption key` | Jalankan `php artisan key:generate`. |
| Tampilan tanpa warna/ikon | Aplikasi memakai CDN Tailwind & Font Awesome — butuh koneksi internet. |
| Login admin gagal | Cek `JUBEMA_ADMIN_EMAIL`/`JUBEMA_ADMIN_PASSWORD` di `.env`, lalu `php artisan db:seed --class=AdminUserSeeder`. |
| `redirect_uri_mismatch` dari Google | URI di Google Cloud Console harus **persis sama** dengan `GOOGLE_REDIRECT_URI` — `http://localhost:8000/auth/google/callback` (perhatikan `http` vs `https`, port, dan tanpa garis miring di akhir). |
| Tombol Google tidak muncul / peringatan kuning | `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` masih kosong di `.env`. Isi, lalu `php artisan config:clear`. |
| Pop up nama & kelas tidak muncul | Akun Google tersebut sudah ada di tabel `users` (dianggap admin) atau belum diarahkan ulang dari callback. Cek `sessions` / log `storage/logs/laravel.log`. |
| Email admin tidak dikenali | Pastikan `JUBEMA_ADMIN_EMAIL` tepat, atau tambahkan lewat `JUBEMA_ADMIN_EMAILS`, lalu `php artisan config:clear`. |
| Perubahan `.env` tidak terbaca | `php artisan config:clear` |

---

## 9. Pengujian Otomatis

Proyek ini dilengkapi berkas `phpunit.xml` dan beberapa tes dasar.

> ⚠️ Tes memakai **database MySQL terpisah** bernama `test`, **bukan** SQLite,
karena ekstensi `pdo_sqlite` tidak selalu tersedia. Buat dulu database tersebut
> dan pastikan user MySQL di `.env` berhak menulis ke sana:
>
> ```bash
> mysql -u root -p -e "CREATE DATABASE test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
> ```
>
> Kredensial tes diatur di `phpunit.xml` (`DB_DATABASE=test`). Database `jubema`
> milikmu **tidak** tersentuh karena setiap tes memakai `RefreshDatabase`.

```bash
php artisan test
```

> Catatan: PHP 8.5 menampilkan notifikasi `Constant PDO::MYSQL_ATTR_SSL_CA is deprecated`.
> Ini berasal dari konfigurasi bawaan Laravel, tidak memengaruhi hasil tes.

| Berkas | Cakupan |
|---|---|
| `tests/Feature/AuthenticationTest.php` | Halaman login, pop up nama & kelas, penyimpanan profil, penolakan tanpa persetujuan, login admin, logout |
| `tests/Feature/GoogleLoginTest.php` | Redirect ke Google, email admin langsung masuk Ruang Admin, email lain mendapat pop up, siswa terdaftar langsung masuk, batalkan akun |
| `tests/Feature/TaskFlowTest.php` | Simpan tugas + 3 langkah kecil, toggle status, hapus, filter hari, BOM CSV |
| `tests/Unit/ModelBehaviourTest.php` | Konstanta hari/prioritas, `Setting::get/put`, sinkronisasi status tugas |

---

## 10. Versi HTML Lama

File `index.html` di root repositori tetap dibiarkan utuh sebagai arsip versi standalone.
Aplikasi Laravel berada di subfolder `laravel/` dan dapat dijalankan berdampingan.
# JUBEMA
