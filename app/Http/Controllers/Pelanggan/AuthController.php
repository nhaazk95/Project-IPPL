<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DetailOrderTemporary;
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

        // Hanya tampilkan meja yang kosong (tersedia)
        $mejas = Meja::where('status', 'tersedia')->orderBy('no_meja')->get();
        return view('pelanggan.login', compact('mejas'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'name_pelanggan' => 'required|string|max:100',
            'no_meja'        => 'required|integer|exists:mejas,no_meja',
        ]);

        $meja = Meja::where('no_meja', $request->no_meja)->firstOrFail();

        // Cek apakah meja masih tersedia
        if ($meja->status === 'terisi') {
            return back()->withErrors(['no_meja' => 'Meja ini sedang terisi. Pilih meja lain.']);
        }

        $loginAt     = now();
        $kdPelanggan = 'PLG-' . $loginAt->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

        $pelanggan = Pelanggan::create([
            'kd_pelanggan'   => $kdPelanggan,
            'name_pelanggan' => $request->name_pelanggan,
            'no_meja'        => $request->no_meja,
            'login_at'       => $loginAt,
        ]);

        $meja->update(['status' => 'terisi']);

        session([
            'pelanggan' => [
                'kd_pelanggan'   => $pelanggan->kd_pelanggan,
                'name_pelanggan' => $pelanggan->name_pelanggan,
                'no_meja'        => $pelanggan->no_meja,
                'login_at'       => $loginAt->toDateTimeString(),
            ],
            'keranjang_count' => 0,
        ]);

        return redirect()->route('pelanggan.beranda');
    }

    public function logout(Request $request)
    {
        $sess = session('pelanggan');

        if ($sess) {
            // Kosongkan keranjang temporary
            DetailOrderTemporary::where('pelanggan_kd', $sess['kd_pelanggan'])->delete();
            // Bebaskan meja
            Meja::where('no_meja', $sess['no_meja'])->update(['status' => 'tersedia']);
        }

        session()->forget(['pelanggan', 'keranjang_count', 'checkout_keterangan']);

        return redirect()->route('pelanggan.login');
    }
}