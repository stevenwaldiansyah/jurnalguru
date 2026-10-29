<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'semester_id',
        'day',
        'start_time',
        'end_time',
    ];


    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function timeRangeLabel(): string
    {
        $start = $this->start_time instanceof \DateTimeInterface
            ? $this->start_time->format('H:i')
            : substr((string) $this->start_time, 0, 5);
        $end = $this->end_time instanceof \DateTimeInterface
            ? $this->end_time->format('H:i')
            : substr((string) $this->end_time, 0, 5);

        return "{$start} - {$end}";
    }
}
