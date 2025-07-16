<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Asumsi Anda memiliki kolom 'role' di tabel 'users'
        // dan 'admin' adalah nilai untuk peran admin.
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Redirect atau tampilkan error jika bukan admin
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}