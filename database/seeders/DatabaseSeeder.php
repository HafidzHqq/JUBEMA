<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database awal JUBEMA: akun Guru BK, tautan Google Workspace,
     * dan satu contoh data siswa agar dashboard tidak kosong.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
