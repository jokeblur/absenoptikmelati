<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'day', 'clock_in', 'clock_out', 'is_holiday'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 