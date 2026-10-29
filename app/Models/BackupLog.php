<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = ['back_time', 'status', 'file_name'];

    protected function casts(): array
    {
        return [
            'back_time' => 'datetime',
        ];
    }
}
