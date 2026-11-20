<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->isLocal() && function_exists('opcache_reset')) {
            @opcache_reset();
        }

        // Bagikan tautan Google Workspace ke semua view supaya konsisten
        // dengan tombol-tombol di aplikasi versi HTML sebelumnya.
        View::composer('*', function ($view) {
            static $links = null;

            // Query ke tabel `settings` cukup sekali per permintaan,
            // bukan setiap kali satu view dirender.
            if ($links === null) {
                $links = [
                    'gform' => config('app.jubema.google_form_url'),
                    'gsheet' => config('app.jubema.google_sheet_url'),
                    'gdrive' => config('app.jubema.google_drive_url'),
                ];

                // Kalau tabel settings sudah ada, nilai tersimpan admin menang.
                try {
                    if (Schema::hasTable('settings')) {
                        $links['gform'] = Setting::get('google_form_url', $links['gform']);
                        $links['gsheet'] = Setting::get('google_sheet_url', $links['gsheet']);
                        $links['gdrive'] = Setting::get('google_drive_url', $links['gdrive']);
                    }
                } catch (\Throwable $e) {
                    // Biarkan nilai default dipakai bila DB belum siap (mis. saat migrate).
                }
            }

            $view->with('workspaceLinks', $links);
        });
    }
}
