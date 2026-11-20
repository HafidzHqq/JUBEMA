<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    public const DAYS = [
        'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
    ];

    public const PRIORITIES = [
        'Penting & Mendesak',
        'Penting & Terjadwal',
        'Tambahan / Pengayaan',
    ];

    protected $fillable = [
        'student_id',
        'day',
        'subject',
        'title',
        'priority',
        'time',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('position');
    }

    /**
     * Tandai selesai bila semua langkah kecil sudah dicentang.
     */
    public function syncCompletionFromSubtasks(): void
    {
        $subtasks = $this->subtasks()->get();

        if ($subtasks->isNotEmpty()) {
            $this->completed = $subtasks->every(fn (Subtask $sub) => $sub->done);
            $this->save();
        }
    }
}
