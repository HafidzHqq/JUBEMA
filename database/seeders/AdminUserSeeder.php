<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun Guru Bimbingan dan Konseling (Admin BK).
 *
 * Kredensial dapat diubah lewat .env:
 *   JUBEMA_ADMIN_NAME, JUBEMA_ADMIN_EMAIL, JUBEMA_ADMIN_PASSWORD
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.jubema.admin_email', 'hesnimaharani31@gmail.com');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('app.jubema.admin_name', 'Hesni Maharani'),
                'password' => Hash::make(config('app.jubema.admin_password', 'Rani010806')),
                'role' => 'admin',
            ]
        );

        $this->command?->info("Akun Admin BK siap: {$email}");
    }
}
