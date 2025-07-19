<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'day', 
        'clock_in', 
        'clock_out', 
        'break_start_time',
        'break_end_time',
        'is_holiday'
    ];

    protected $casts = [
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'break_start_time' => 'datetime:H:i',
        'break_end_time' => 'datetime:H:i',
        'is_holiday' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the work days available
     */
    public static function getWorkDays()
    {
        return [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu'
        ];
    }
} 