<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        // Dapatkan user yang baru saja login
        $user = Auth::user();

        // Periksa peran user dan redirect sesuai
        if ($user && $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard')); // Menggunakan nama rute admin
        } else {
            return redirect()->intended(route('employee.dashboard')); // Atau rute dashboard karyawan Anda
        }

        // Jika tidak ada role spesifik, fallback ke default
        // return redirect()->intended(route('dashboard')); // Ini adalah default Breeze jika tidak diubah
    

        // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
