<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\DetailOrder;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'semua');
        $query  = Order::with(['meja', 'detailOrders.menu']);

        if ($status !== 'semua') {
            $query->where('status_order', $status);
        } else {
            $query->whereIn('status_order', ['pending', 'diproses']);
        }

        $orders = $query->orderByDesc('waktu')->paginate(15);

        return view('kasir.order', compact('orders', 'status'));
    }

    public function detail(string $kd_order)
    {
        $order = Order::with(['meja', 'detailOrders.menu'])->findOrFail($kd_order);
        return view('kasir.order-detail', compact('order'));
    }

    /**
     * Proses pembayaran oleh kasir — return JSON untuk modal struk
     */
    public function prosesBayar(Request $request, string $kd_order)
    {
        $request->validate([
            'metode'       => 'nullable|in:cash,qris',
            'jumlah_bayar' => 'nullable|integer|min:0',
        ]);

        $order = Order::with('detailOrders.menu', 'meja')->findOrFail($kd_order);

        if ($order->status_order === 'selesai') {
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

    public function proses(string $kd_order)
    {
        $order = Order::findOrFail($kd_order);
        $order->updateStatus('diproses');
        return back()->with('success', 'Order #' . $kd_order . ' sedang diproses.');
    }

    public function selesai(string $kd_order)
    {
        $order = Order::findOrFail($kd_order);
        $order->updateStatus('siap');
        return back()->with('success', 'Order siap diantar ke pelanggan.');
    }
}