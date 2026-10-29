<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = ['name', 'academic_year'];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function displayLabel(): string
    {
        return "{$this->name} — {$this->academic_year}";
    }
}
