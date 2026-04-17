<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PelangganSession
{
    public function handle(Request $request, Closure $next)
    {
        // cek session pelanggan
        if (!session()->has('pelanggan')) {
            return redirect()->route('pelanggan.login')
                ->with('error', 'Silakan masuk terlebih dahulu');
        }

        return $next($request);
    }
}