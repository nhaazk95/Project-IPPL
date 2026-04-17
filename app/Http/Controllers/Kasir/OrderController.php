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
     * FIX: method ini didaftarkan di route tapi tidak ada.
     * Proses pembayaran oleh kasir — buat Transaksi & update status order.
     */
    public function prosesBayar(Request $request, string $kd_order)
    {
        $request->validate([
            'metode' => 'required|in:cash,qris',
        ]);

        $order = Order::with('detailOrders')->findOrFail($kd_order);

        if ($order->status_order === 'selesai') {
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

        // Update semua detail order → link ke transaksi & status selesai
        $order->detailOrders()->update([
            'transaksi_kd'  => $transaksi->kd_transaksi,
            'status_detail' => 'selesai',
        ]);

        $order->updateStatus('selesai');

        return redirect()->route('kasir.struk', $transaksi->kd_transaksi)
            ->with('success', 'Pembayaran berhasil diproses!');
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
