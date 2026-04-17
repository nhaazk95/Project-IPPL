<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\DetailOrderTemporary;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $kdPelanggan = session('pelanggan.kd_pelanggan');

        // Kategori untuk ditampilkan di beranda
        $kategoris = Kategori::withCount(['menus' => function ($q) {
            $q->where('status', 'tersedia');
        }])->get();

        // Best seller: 5 menu paling mahal / paling banyak dipesan
        // (sesuaikan logika bisnis Anda)
        $bestSeller = Menu::where('status', 'tersedia')
            ->orderBy('harga', 'desc')
            ->limit(5)
            ->get();

        // Update session keranjang_count — PAKAI kolom yang benar sesuai DB
        // Cek nama kolom yang ada di tabel detail_order_temporaries
        try {
            $keranjangCount = DetailOrderTemporary::where('kd_pelanggan', $kdPelanggan)->count();
        } catch (\Exception $e) {
            // Fallback jika nama kolom berbeda
            try {
                $keranjangCount = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)->count();
            } catch (\Exception $e2) {
                $keranjangCount = 0;
            }
        }

        // Simpan ke session agar bisa dipakai di layout tanpa query DB
        session(['keranjang_count' => $keranjangCount]);

        return view('pelanggan.beranda', compact('kategoris', 'bestSeller'));
    }
}