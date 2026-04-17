<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('pelanggan')) {
            return redirect()->route('pelanggan.beranda');
        }

        $mejas = Meja::orderBy('no_meja')->get();
        return view('pelanggan.login', compact('mejas'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'name_pelanggan' => 'required|string|max:100',
            'no_meja'        => 'required|integer|exists:mejas,no_meja',
        ]);

        $meja = Meja::where('no_meja', $request->no_meja)->firstOrFail();

        $kdPelanggan = 'PLG-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

        $pelanggan = Pelanggan::create([
            'kd_pelanggan'   => $kdPelanggan,
            'name_pelanggan' => $request->name_pelanggan,
            'no_meja'        => $request->no_meja,
        ]);

        $meja->update(['status' => 'terisi']);

        // FIX: simpan 'name_pelanggan' (bukan 'name') agar konsisten
        // dengan semua tempat yang baca session('pelanggan.name_pelanggan')
        session([
            'pelanggan' => [
                'kd_pelanggan'   => $pelanggan->kd_pelanggan,
                'name_pelanggan' => $pelanggan->name_pelanggan,
                'no_meja'        => $pelanggan->no_meja,
            ],
            'keranjang_count' => 0,  // FIX: konsisten dengan key yg dipakai KeranjangController
        ]);

        return redirect()->route('pelanggan.beranda');
    }

    public function logout(Request $request)
    {
        $sess = session('pelanggan');

        if ($sess) {
            Meja::where('no_meja', $sess['no_meja'])->update(['status' => 'kosong']);
        }

        session()->forget(['pelanggan', 'keranjang_count']);  // FIX: konsisten key

        return redirect()->route('pelanggan.login');
    }
}
