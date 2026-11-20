<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Identitas siswa JUBEMA (Nama Panggilan + Kelas, tanpa data sensitif).
 */
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kelas',
        'google_id',
        'google_email',
        'google_avatar',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function reflections(): HasMany
    {
        return $this->hasMany(Reflection::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Cari siswa berdasarkan nama panggilan + kelas, buat bila belum ada.
     * Dipakai saat login siswa (tanpa kata sandi).
     */
    public static function findOrCreateByNameAndKelas(string $name, string $kelas): self
    {
        return static::firstOrCreate([
            'name' => trim($name),
            'kelas' => $kelas,
        ]);
    }

    /**
     * Tautkan identitas Google ke baris siswa ini (dipanggil dari pop up
     * Nama Panggilan + Kelas setelah login Google berhasil).
     *
     * Tidak menimpa bila baris siswa sudah tertaut ke akun Google lain,
     * supaya satu akun Google tidak bisa mengambil alih profil siswa lain.
     */
    public function linkGoogle(?string $googleId, ?string $email = null, ?string $avatar = null): void
    {
        if (blank($googleId)) {
            return;
        }

        if (filled($this->google_id) && $this->google_id !== $googleId) {
            return;
        }

        $this->forceFill([
            'google_id' => $googleId,
            'google_email' => $email,
            'google_avatar' => $avatar,
        ])->save();
    }
}
