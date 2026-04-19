<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['pelanggan', 'detailOrders.menu', 'meja'])
            ->whereIn('status_order', ['pending', 'diproses'])
            ->whereDoesntHave('transaksi');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kd_order', 'like', "%{$s}%")
                  ->orWhere('nama_user', 'like', "%{$s}%")
                  ->orWhereHas('pelanggan', fn($p) => $p->where('name_pelanggan', 'like', "%{$s}%"));
            });
        }

        $orders = $query->orderByDesc('waktu')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        return view('kasir.transaksi', compact('orders'));
    }

    public function laporan(Request $request)
    {
        $semuaTransaksi = Transaksi::where('user_kd', auth()->user()->kd_user)
            ->orderByDesc('tanggal')->get();

        $query = Transaksi::with(['order.meja', 'kasir'])
            ->where('user_kd', auth()->user()->kd_user);

        if ($request->filled('kd_transaksi')) {
            $query->where('kd_transaksi', $request->kd_transaksi);
        }

        $transaksis = $query->orderByDesc('tanggal')->get();

        return view('kasir.laporan', compact('transaksis', 'semuaTransaksi'));
    }

    public function struk(string $kd_transaksi)
    {
        $transaksi   = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);
        $jumlahBayar = session('jumlah_bayar', $transaksi->total_harga);

        return view('kasir.struk', compact('transaksi', 'jumlahBayar'));
    }
}
