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
        'answers',
        'status',
        'scheduled_at',
        'scheduled_location',
        'scheduled_note',
    ];

    protected function casts(): array
    {
        return [
            'focus_level' => 'integer',
            'answers' => 'array',
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

    public function displayTopic(): string
    {
        if (filled($this->topic)) {
            return $this->topic;
        }

        foreach ($this->formattedAnswers() as $ans) {
            if (filled($ans['value'] ?? null)) {
                return (string) $ans['value'];
            }
        }

        return 'Permintaan Konsultasi';
    }

    /**
     * Daftar jawaban terstruktur baik dari format dinamis baru maupun kolom bawaan lama.
     *
     * @return list<array{id: string, label: string, type: string, value: string}>
     */
    public function formattedAnswers(): array
    {
        if (is_array($this->answers) && count($this->answers) > 0) {
            $result = [];
            foreach ($this->answers as $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                $val = isset($entry['value']) ? trim((string) $entry['value']) : '';
                if ($val === '') {
                    continue;
                }
                $result[] = [
                    'id' => (string) ($entry['id'] ?? ''),
                    'label' => (string) ($entry['label'] ?? 'Pertanyaan'),
                    'type' => (string) ($entry['type'] ?? 'text'),
                    'value' => $val,
                ];
            }

            if (count($result) > 0) {
                return $result;
            }
        }

        $legacy = [];
        if (filled($this->difficulty)) {
            $legacy[] = ['id' => 'difficulty', 'label' => 'Kesulitan utama', 'type' => 'radio', 'value' => (string) $this->difficulty];
        }
        if ($this->focus_level) {
            $legacy[] = ['id' => 'focus_level', 'label' => 'Skala fokus', 'type' => 'scale', 'value' => $this->focus_level.' / 4'];
        }
        if (filled($this->topic)) {
            $legacy[] = ['id' => 'topic', 'label' => 'Topik diskusi', 'type' => 'radio', 'value' => (string) $this->topic];
        }
        if (filled($this->bring_journal)) {
            $legacy[] = ['id' => 'bring_journal', 'label' => 'Catatan jurnal', 'type' => 'radio', 'value' => (string) $this->bring_journal];
        }
        if (filled($this->mode)) {
            $legacy[] = ['id' => 'mode', 'label' => 'Waktu pilihan', 'type' => 'radio', 'value' => (string) $this->mode];
        }
        if (filled($this->consultation_method)) {
            $legacy[] = ['id' => 'consultation_method', 'label' => 'Cara pendampingan', 'type' => 'radio', 'value' => (string) $this->consultation_method];
        }
        if (filled($this->message)) {
            $legacy[] = ['id' => 'message', 'label' => 'Catatan tambahan', 'type' => 'textarea', 'value' => (string) $this->message];
        }

        return $legacy;
    }
}
