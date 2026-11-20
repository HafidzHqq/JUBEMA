<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week',
        'mood',
        'good',
        'obstacle',
        'plan',
        'shared_with_bk',
    ];

    protected function casts(): array
    {
        return [
            'shared_with_bk' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeSharedWithBk($query)
    {
        return $query->where('shared_with_bk', true);
    }
}
