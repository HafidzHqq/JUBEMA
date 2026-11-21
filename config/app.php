<?php

return [

    'name' => env('APP_NAME', 'JUBEMA'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),

    'locale' => env('APP_LOCALE', 'id'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Khusus JUBEMA
    |--------------------------------------------------------------------------
    | Nilai default tautan Google Workspace. Nilai ini bisa diubah oleh
    | admin lewat halaman "Pengaturan Tautan Google Workspace" dan akan
    | disimpan di tabel `settings`.
    */
    'jubema' => [
        'google_form_url' => env('JUBEMA_GFORM_URL', 'https://forms.gle/Qg653vMCXYTVmg1e9'),
        'google_sheet_url' => env('JUBEMA_GSHEET_URL', 'https://docs.google.com/spreadsheets'),
        'google_drive_url' => env('JUBEMA_GDRIVE_URL', 'https://drive.google.com'),

        // Kredensial akun Guru BK (Admin) yang dibuat oleh AdminUserSeeder.
        // Tambahkan kunci ini di config supaya tetap aman ketika
        // `php artisan config:cache` dijalankan di server produksi.
        'admin_name' => env('JUBEMA_ADMIN_NAME', 'Hesni Maharani'),
        'admin_email' => env('JUBEMA_ADMIN_EMAIL', 'hesnimaharani31@gmail.com'),
        'admin_password' => env('JUBEMA_ADMIN_PASSWORD', 'Rani010806'),

        // Daftar email Google yang boleh masuk sebagai Admin BK lewat
        // tombol "Masuk dengan Google" (dipisah koma di .env).
        // Email pada `admin_email` di atas otomatis ikut dianggap admin.
        'admin_emails' => array_values(array_filter(array_map(
            'strtolower',
            array_map('trim', explode(',', (string) env('JUBEMA_ADMIN_EMAILS', '')))
        ))),
    ],

];
