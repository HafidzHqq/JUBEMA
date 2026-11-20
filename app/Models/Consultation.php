<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'terkirim' => 'Terkirim ke Guru BK',
        'menunggu_jadwal_bk' => 'Menunggu Jadwal BK',
        'sudah_dijadwalkan' => 'Sudah Dijadwalkan',
    ];

    public const KELAS_OPTIONS = ['VII', 'VIII', 'IX'];

    public const DIFFICULTIES = [
        'Sulit menyusun jadwal atau rencana belajar mingguan',
        'Sering menunda-nunda pengerjaan tugas (prokrastinasi)',
        'Tugas menumpuk menjelang tenggat waktu (deadline)',
        'Sulit fokus atau sering terganggu saat belajar',
    ];

    public const TOPICS = [
        'Meminta masukan/bimbingan terkait lembar Jurnal Belajar Mandiri saya',
        'Cara mengatasi kebiasaan menunda tugas sekolah',
        'Strategi memecah tugas besar menjadi langkah-langkah kecil',
        'Mengatur jadwal harian agar tidak keteteran menghadapi banyak mata pelajaran',
        'Konsultasi masalah pribadi/belajar lainnya',
    ];

    public const JOURNAL_OPTIONS = [
        'Ya, saya membawa/membagikan catatan saya',
        'Tidak, saya ingin langsung berdiskusi secara lisan',
    ];

    public const PREFERRED_TIMES = [
        'Jam istirahat sekolah',
        'Jam sepulang sekolah (sebelum jam pulang utama)',
        'Waktu khusus yang ditentukan oleh Guru BK',
    ];

    public const METHODS = [
        'Konsultasi tatap muka di Ruang BK',
        'Konsultasi daring via Google Meet',
        'Bantuan lisan singkat di kelas',
        'Hanya berupa catatan / pesan tertulis',
    ];

    protected $fillable = [
        'student_id',
        'respondent_name',
        'respondent_kelas',
        'email',
        'difficulty',
        'focus_level',
        'topic',
        'bring_journal',
        'mode',
        'consultation_method',
        'message',
        'status',
        'scheduled_at',
    ];

    protected function casts(): array
    {
        return [
            'focus_level' => 'integer',
            'scheduled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function displayName(): string
    {
        return $this->respondent_name ?: ($this->student?->name ?? 'Siswa');
    }

    public function displayKelas(): string
    {
        if (filled($this->respondent_kelas)) {
            return str_starts_with($this->respondent_kelas, 'Kelas ')
                ? $this->respondent_kelas
                : 'Kelas '.$this->respondent_kelas;
        }

        return $this->student?->kelas ?? '-';
    }
}
