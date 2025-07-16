<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permission_date',
        'reason',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'permission_date' => 'date',
    ];

    // Relasi ke User (karyawan yang mengajukan izin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}