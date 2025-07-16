<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
   

    // ... (fillable, hidden, casts) ...

  

    // Relasi ke Permission (pengajuan izin yang dibuat oleh user ini)
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    // Relasi ke Leave
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
    public function attendances()
{
    return $this->hasMany(Attendance::class);
}

    public function workSchedules()
    {
        return $this->hasMany(\App\Models\WorkSchedule::class);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'custom_clock_in_time',
        'custom_clock_out_time',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    // Relationship
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}