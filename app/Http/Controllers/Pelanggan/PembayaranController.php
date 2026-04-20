<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request, $kd_order)
    {
        $order = Order::with(['detailOrders.menu', 'meja'])->findOrFail($kd_order);

        $totalHarga = $order->detailOrders->sum('sub_total');

        // ?metode=qris atau ?metode=kasir — null berarti tampilkan pilihan
        $metode = $request->query('metode');

        // Simpan metode ke order jika dipilih
        if ($metode && in_array($metode, ['qris', 'kasir'])) {
            $order->update(['metode_bayar' => $metode]);
        }

        return view('pelanggan.pembayaran', compact('order', 'totalHarga', 'metode'));
    }
}