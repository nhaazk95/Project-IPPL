<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\Meja;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Order yang siap dibayar (status siap / selesai)
        $orderSiap = Order::with(['meja', 'detailOrders.menu'])
            ->whereIn('status_order', ['siap', 'diproses'])
            ->whereDoesntHave('transaksi')
            ->orderByDesc('waktu')
            ->get();

        // Riwayat transaksi hari ini oleh kasir ini
        $riwayat = Transaksi::with(['order.meja', 'kasir'])
            ->whereDate('tanggal', today())
            ->where('user_kd', auth()->user()->kd_user)
            ->orderByDesc('waktu')
            ->paginate(10);

        return view('kasir.transaksi', compact('orderSiap', 'riwayat'));
    }

    public function bayar(Request $request, string $kd_order)
    {
        $request->validate([
            'jumlah_bayar' => 'required|integer|min:0',
        ]);

        $order = Order::with('detailOrders')->findOrFail($kd_order);

        if ($order->transaksi) {
            return back()->with('error', 'Order ini sudah dibayar.');
        }

        $transaksi = new Transaksi();
        $result = $transaksi->prosesPembayaran($kd_order, auth()->user()->kd_user);

        // Update status meja jadi tersedia
        if ($order->meja) {
            $order->meja->setStatus('tersedia');
        }

        $kembalian = $request->jumlah_bayar - $result->total_harga;

        return redirect()
            ->route('kasir.transaksi.struk', $result->kd_transaksi)
            ->with('kembalian', $kembalian)
            ->with('jumlah_bayar', $request->jumlah_bayar);
    }

    public function struk(string $kd_transaksi)
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);

        $kembalian = session('kembalian', 0);
        $jumlahBayar = session('jumlah_bayar', $transaksi->total_harga);

        return view('kasir.struk', compact('transaksi', 'kembalian', 'jumlahBayar'));
    }

    public function laporan(Request $request)
    {
        $dari = $request->dari ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $transaksis = Transaksi::with(['order.meja', 'kasir'])
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->where('user_kd', auth()->user()->kd_user)
            ->orderByDesc('tanggal')
            ->paginate(10);

        $totalTransaksi = $transaksis->total();
        $totalPendapatan = $transaksis->sum('total_harga');

        return view('kasir.laporan', compact(
            'transaksis',
            'totalTransaksi',
            'totalPendapatan',
            'dari',
            'sampai'
        ));
    }
}