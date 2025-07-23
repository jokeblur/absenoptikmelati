<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function saveSubscription(Request $request)
    {
        $user = Auth::user();
        $user->push_subscription = $request->all();
        $user->save();
        return response()->json(['success' => true]);
    }
} 