<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $sess   = session('pelanggan');
        $orders = Order::with(['detailOrders.menu', 'transaksi'])
            ->where('kd_pelanggan', $sess['kd_pelanggan'])  // FIX: kolom di tabel orders adalah kd_pelanggan
            ->latest('waktu')
            ->get();

        return view('pelanggan.pesanan', compact('orders'));
    }

    public function pembayaran(string $kd_order, Request $request)
    {
        $sess = session('pelanggan');

        $order = Order::with('detailOrders.menu')
            ->where('kd_pelanggan', $sess['kd_pelanggan'])  // FIX: sama
            ->where('kd_order', $kd_order)
            ->firstOrFail();

        $totalHarga = $order->detailOrders->sum('sub_total');
        $metode     = $request->query('metode');

        return view('pelanggan.pembayaran', compact('order', 'totalHarga', 'metode'));
    }

    public function konfirmasi(Request $request, string $kd_order)
    {
        $request->validate([
            'metode' => 'required|in:cash,qris',
        ]);

        $sess  = session('pelanggan');
        $order = Order::where('kd_pelanggan', $sess['kd_pelanggan'])  // FIX: konsisten pakai kd_pelanggan
            ->where('kd_order', $kd_order)
            ->firstOrFail();

        $order->update(['keterangan' => ($order->keterangan ? $order->keterangan . ' | ' : '') . 'Metode: ' . strtoupper($request->metode)]);

        return response()->json(['success' => true, 'message' => 'Konfirmasi diterima. Kasir akan memproses pembayaran.']);
    }
}
