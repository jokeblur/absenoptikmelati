<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'timestamp',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'break_late_minutes',
        'type',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'status_in',
        'status_out',
        'late_minutes',
        'photo_in',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'timestamp' => 'datetime',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Branch melalui User
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_in', $status)->orWhere('status_out', $status);
    }

    /**
     * Accessor untuk timestamp (menggunakan check_in sebagai default)
     */
    public function getTimestampAttribute()
    {
        return $this->check_in ?? $this->date;
    }

    /**
     * Accessor untuk type
     */
    public function getTypeAttribute()
    {
        return $this->check_in ? 'check_in' : 'check_out';
    }

    /**
     * Accessor untuk status
     */
    public function getStatusAttribute()
    {
        return $this->status_in ?? $this->status_out ?? 'on_time';
    }

    /**
     * Accessor untuk latitude
     */
    public function getLatitudeAttribute()
    {
        return $this->latitude_in ?? $this->latitude_out;
    }

    /**
     * Accessor untuk longitude
     */
    public function getLongitudeAttribute()
    {
        return $this->longitude_in ?? $this->longitude_out;
    }
}