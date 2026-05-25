<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
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
     * Proses pembayaran oleh admin — return JSON untuk modal struk
     */
    public function bayar(Request $request, string $kd_order)
    {
        $request->validate([
            'jumlah_bayar' => 'nullable|integer|min:0',
        ]);

        $order = Order::with('detailOrders.menu', 'meja')->findOrFail($kd_order);

        if ($order->transaksi) {
            return response()->json(['error' => 'Order ini sudah dibayar.'], 422);
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

        if ($order->meja) {
            $order->meja->update(['status' => 'tersedia']);
        }

        $jumlahBayar = $request->input('jumlah_bayar', $total);

        return response()->json([
            'kd_transaksi' => $transaksi->kd_transaksi,
            'tanggal'      => now()->format('d/m/Y'),
            'waktu'        => now()->format('H:i'),
            'kasir'        => Auth::user()->name,
            'no_meja'      => $order->no_meja,
            'nama_user'    => $order->nama_user,
            'total_harga'  => $total,
            'jumlah_bayar' => $jumlahBayar,
            'kembalian'    => max(0, $jumlahBayar - $total),
            'items'        => $order->detailOrders->map(fn($d) => [
                'nama'      => $d->menu->name_menu ?? '-',
                'qty'       => $d->total,
                'sub_total' => $d->sub_total,
            ]),
        ]);
    }

    public function show(string $kd_transaksi)
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);

        return view('admin.transaksi.show', compact('transaksi'));
    }
}