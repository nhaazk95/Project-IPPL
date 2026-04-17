<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    /**
     * Halaman Transaksi — pilih order, proses bayar (sama seperti kasir)
     */
    public function index(Request $request)
    {
        $query = Order::with(['meja', 'detailOrders.menu'])
            ->whereIn('status_order', ['pending', 'diproses'])
            ->whereDoesntHave('transaksi');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('kd_order', 'like', "%{$q}%")
                   ->orWhere('nama_user', 'like', "%{$q}%");
            });
        }

        $orders = $query->orderByDesc('waktu')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        return view('admin.transaksi.index', compact('orders'));
    }

    /**
     * Proses pembayaran oleh admin (sama seperti kasir.prosesBayar)
     */
    public function bayar(Request $request, string $kd_order)
    {
        $request->validate([
            'jumlah_bayar' => 'nullable|integer|min:0',
        ]);

        $order = Order::with('detailOrders')->findOrFail($kd_order);

        if ($order->transaksi) {
            return back()->with('error', 'Order ini sudah dibayar.');
        }

        $total = $order->detailOrders->sum('sub_total');

        $transaksi = Transaksi::create([
            'kd_transaksi' => 'TRX-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'order_kd'     => $kd_order,
            'user_kd'      => Auth::user()->kd_user,
            'total_harga'  => $total,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        $order->detailOrders()->update([
            'transaksi_kd'  => $transaksi->kd_transaksi,
            'status_detail' => 'selesai',
        ]);

        $order->updateStatus('selesai');

        // Update status meja
        if ($order->meja) {
            $order->meja->update(['status' => 'tersedia']);
        }

        return redirect()
            ->route('admin.transaksi.index')
            ->with('success', 'Transaksi ' . $transaksi->kd_transaksi . ' berhasil disimpan!');
    }

    /**
     * Detail transaksi
     */
    public function show(string $kd_transaksi)
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);

        return view('admin.transaksi.show', compact('transaksi'));
    }
}