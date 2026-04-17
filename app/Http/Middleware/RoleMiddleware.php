<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:kasir')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $namaLevel = strtolower($user->level->nama_level ?? '');

        if ($namaLevel !== strtolower($role)) {
            // Redirect ke dashboard yang sesuai role user
            if ($namaLevel === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($namaLevel === 'kasir') {
                return redirect()->route('kasir.dashboard');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}