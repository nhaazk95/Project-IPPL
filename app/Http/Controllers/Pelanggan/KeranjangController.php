<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DetailOrderTemporary;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KeranjangController extends Controller
{
    private function kdPelanggan(): string
    {
        return session('pelanggan.kd_pelanggan', '');
    }

    public function index()
    {
        $kdPelanggan = $this->kdPelanggan();

        $keranjang = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)
            ->with('menu')
            ->get();

        session(['keranjang_count' => $keranjang->count()]);

        return view('pelanggan.keranjang', compact('keranjang'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'kd_menu' => 'required|exists:menus,kd_menu',
            'jumlah'  => 'required|integer|min:1|max:99',
        ]);

        $kdPelanggan = $this->kdPelanggan();
        $menu        = Menu::findOrFail($request->kd_menu);

        $existing = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)
            ->where('menu_kd', $request->kd_menu)
            ->first();

        if ($existing) {
            $jumlahBaru = $existing->total + $request->jumlah;
            $existing->update([
                'total'      => $jumlahBaru,
                'sub_total'  => $menu->harga * $jumlahBaru,
                'keterangan' => $request->keterangan ?? $existing->keterangan,
            ]);
        } else {
            DetailOrderTemporary::create([
                'kd_detail'    => 'TMP-' . strtoupper(Str::random(8)),
                'pelanggan_kd' => $kdPelanggan,
                'menu_kd'      => $request->kd_menu,
                'total'        => $request->jumlah,
                'sub_total'    => $menu->harga * $request->jumlah,
                'keterangan'   => $request->keterangan,
            ]);
        }

        session(['keranjang_count' => DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)->count()]);

        return redirect()->route('pelanggan.menu.detail', $request->kd_menu)
            ->with('success', 'Menu berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, string $kdDetail)
    {
        $detail = DetailOrderTemporary::findOrFail($kdDetail);
        $menu   = Menu::findOrFail($detail->menu_kd);
        $aksi   = $request->aksi;

        $jumlahBaru = $aksi === 'tambah'
            ? $detail->total + 1
            : max(1, $detail->total - 1);

        $detail->update([
            'total'     => $jumlahBaru,
            'sub_total' => $menu->harga * $jumlahBaru,
        ]);

        session(['keranjang_count' => DetailOrderTemporary::where('pelanggan_kd', $this->kdPelanggan())->count()]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function hapus(string $kdDetail)
    {
        DetailOrderTemporary::findOrFail($kdDetail)->delete();

        session(['keranjang_count' => DetailOrderTemporary::where('pelanggan_kd', $this->kdPelanggan())->count()]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Checkout: simpan keterangan ke session, redirect ke halaman pilih metode.
     * Order belum dibuat — belum masuk ke kasir.
     */
    public function checkout(Request $request)
    {
        $kdPelanggan = $this->kdPelanggan();

        $keranjang = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)->with('menu')->get();

        if ($keranjang->isEmpty()) {
            return redirect()->route('pelanggan.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        // Simpan keterangan ke session, belum buat order
        session(['checkout_keterangan' => $request->keterangan]);

        // Redirect ke halaman pembayaran (pilih metode) — pakai kd_temp sebagai penanda
        return redirect()->route('pelanggan.pembayaran.preview');
    }
}