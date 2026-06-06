<?php

namespace App\Http\Middleware;

use App\Models\DetailOrderTemporary;
use App\Models\Meja;
use Closure;
use Illuminate\Http\Request;

class PelangganSession
{
    public function handle(Request $request, Closure $next)
    {
        $sess = session('pelanggan');

        if (!$sess) {
            return redirect()->route('pelanggan.login')
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Validasi: cek apakah meja masih terisi di DB
        // Jika admin sudah force-kosongkan meja, session ini dianggap tidak valid
        $meja = Meja::where('no_meja', $sess['no_meja'])->first();

        if (!$meja || $meja->status === 'tersedia') {
            // Cek apakah ada order yang baru selesai dibayar (pelanggan sedang lihat nota)
            $kdPelanggan = $sess['kd_pelanggan'];
            $loginAt     = $sess['login_at'] ?? null;

            $adaOrderSelesai = \App\Models\Order::where('kd_pelanggan', $kdPelanggan)
                ->where('status_order', 'selesai')
                ->when($loginAt, fn($q) => $q->where('waktu', '>=', $loginAt))
                ->exists();

            // Kalau ada order yang baru selesai, jangan auto-logout dulu
            // Biarkan pelanggan lihat nota, logout manual saat klik Keluar
            if ($adaOrderSelesai && $meja && $meja->status === 'tersedia') {
                // Izinkan akses tapi tandai sesi akan berakhir
                // Pelanggan bisa logout sendiri
                return $next($request);
            }

            // Meja sudah dikosongkan admin / tidak ada — force logout
            \App\Models\DetailOrderTemporary::where('pelanggan_kd', $sess['kd_pelanggan'])->delete();
            session()->forget(['pelanggan', 'keranjang_count', 'checkout_keterangan']);

            return redirect()->route('pelanggan.login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        }

        return $next($request);
    }
}