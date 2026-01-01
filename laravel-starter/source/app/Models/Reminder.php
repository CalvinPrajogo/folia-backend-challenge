<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;


    // Which attributes are mass assignable
    protected $fillable = [
        'title',
        'description',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_days',
        'reminder_time',
        'start_date',
        'end_date',
    ];


    // Tells laravel how to convert database values to PHP types
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'recurrence_days' => 'array',
        ];
    }
}
