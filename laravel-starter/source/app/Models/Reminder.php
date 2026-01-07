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

    // Get occurences of a reminder in a date range
    public function getOccurrences(\DateTime $from, \DateTime $to): array
    {
        $occurrences = [];

        # Calculate the actual bounds of the search range
        $actualStart = max($from, $this->start_date);
        $actualEnd = $this->end_date ? min($to, $this->end_date) : $to; # use ternary in case there is no end date

        # Check that there is actually a range to search
        if ($actualStart > $actualEnd) {
            return [];
        }

        if ($this->recurrence_type === "none") {
            # One time reminder
            if ($this->start_date->between($actualStart, $actualEnd)) {
                $occurrences[] = [
                    "occurrence_date" => $this->start_date->format('Y-m-d'),
                    "id" => $this->id,
                    "title" => $this->title,
                    "description" => $this->description
                ];
            }

        } elseif ($this->recurrence_type === "daily") {
            # Daily reminder
            $current = $actualStart->copy();  // Make a copy to not modify actualStart
    
            while ($current->lessThanOrEqualTo($actualEnd)) {
                $occurrences[] = [
                    "occurrence_date" => $current->format('Y-m-d'),
                    "id" => $this->id,
                    "title" => $this->title,
                    "description" => $this->description
                ];
                
                $current->addDay();  // Move to next day
            }

        } elseif ($this->recurrence_type === "interval") {
            # Every n days reminder
            $current = $this->start_date->copy();  // Start from reminder's start
            
            // Skip ahead to the first occurrence >= actualStart
            while ($current->lessThan($actualStart)) {
                $current->addDays($this->recurrence_interval);
            }
            
            // Now add occurrences within the range
            while ($current->lessThanOrEqualTo($actualEnd)) {
                $occurrences[] = [
                    "occurrence_date" => $current->format('Y-m-d'),
                    "id" => $this->id,
                    "title" => $this->title,
                    "description" => $this->description
                ];
                
                $current->addDays($this->recurrence_interval);
            }
        } elseif ($this->recurrence_type === "weekly") {
            # Weekly reminder
            $current = $actualStart->copy();
    
            while ($current->lessThanOrEqualTo($actualEnd)) {
                // Get the day name (e.g., "monday", "tuesday")
                $dayName = strtolower($current->format('l'));
                
                // Check if this day is in the recurrence_days array
                if (in_array($dayName, $this->recurrence_days)) {
                    $occurrences[] = [
                        "occurrence_date" => $current->format('Y-m-d'),
                        "id" => $this->id,
                        "title" => $this->title,
                        "description" => $this->description
                    ];
                }
                
                $current->addDay();  // Check next day
            }
        } 

        return $occurrences;
    }
}
