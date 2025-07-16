<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'user_id',
        'related_id',
        'related_type',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (Leave or Permission).
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get notifications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Create a notification for leave request.
     */
    public static function createLeaveNotification(Leave $leave)
    {
        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            self::create([
                'type' => 'leave_request',
                'title' => 'Pengajuan Cuti Baru',
                'message' => "Karyawan {$leave->user->name} mengajukan cuti dari {$leave->start_date} sampai {$leave->end_date}",
                'user_id' => $admin->id,
                'related_id' => $leave->id,
                'related_type' => Leave::class,
                'is_read' => false
            ]);
        }
    }

    /**
     * Create a notification for permission request.
     */
    public static function createPermissionNotification(Permission $permission)
    {
        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            self::create([
                'type' => 'permission_request',
                'title' => 'Pengajuan Izin Baru',
                'message' => "Karyawan {$permission->user->name} mengajukan izin pada {$permission->date}",
                'user_id' => $admin->id,
                'related_id' => $permission->id,
                'related_type' => Permission::class,
                'is_read' => false
            ]);
        }
    }
}
