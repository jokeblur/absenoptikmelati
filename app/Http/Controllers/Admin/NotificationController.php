<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Tampilkan notifikasi yang belum dibaca
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    // Tandai notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    // Ambil notifikasi terbaru untuk dropdown (AJAX)
    public function latest(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false) // Hanya notifikasi yang belum dibaca
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
