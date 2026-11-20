<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Tautan Google Workspace default (Form, Spreadsheet, Drive).
 * Dapat diubah Guru BK dari menu "Pengaturan Tautan Google Workspace".
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'google_form_url' => config('app.jubema.google_form_url'),
            'google_sheet_url' => config('app.jubema.google_sheet_url'),
            'google_drive_url' => config('app.jubema.google_drive_url'),
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command?->info('Tautan Google Workspace default tersimpan.');
    }
}
