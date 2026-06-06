<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            $query->whereIn('status_order', ['pending', 'diproses', 'siap']);
        }

        $orders = $query->orderByDesc('waktu')->paginate(15);
        return view('kasir.order', compact('orders', 'status'));
    }

    public function detail(string $kd_order)
    {
        $order = Order::with(['meja', 'detailOrders.menu'])->findOrFail($kd_order);
        return view('kasir.order-detail', compact('order'));
    }

    public function prosesBayar(Request $request, string $kd_order)
    {
        $request->validate([
            'metode'       => 'required|in:cash,debit,qris',
            'jumlah_bayar' => 'nullable|numeric|min:0',
        ]);

        $order = Order::with('detailOrders')->findOrFail($kd_order);

        // Kalau sudah dibayar, redirect ke struk-nya saja
        if ($order->transaksi) {
            return redirect()
                ->route('kasir.struk', $order->transaksi->kd_transaksi)
                ->with('info', 'Order ini sudah dibayar sebelumnya.');
        }

        $total = $order->detailOrders->sum('sub_total');
        $metode = $request->metode;

        // Untuk cash: validasi jumlah bayar harus >= total
        if ($metode === 'cash') {
            $jumlahBayar = (int) $request->jumlah_bayar;
            if ($jumlahBayar < $total) {
                return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar kurang dari total tagihan.']);
            }
            session(['jumlah_bayar' => $jumlahBayar]);
        } else {
            // QRIS / Debit — jumlah bayar = total
            session(['jumlah_bayar' => $total]);
        }

        $kdTrx = 'TRX-' . now()->format('YmdHis') . '-' . rand(100, 999);

        $transaksi = Transaksi::create([
            'kd_transaksi' => $kdTrx,
            'order_kd'     => $kd_order,
            'user_kd'      => Auth::user()->kd_user,
            'total_harga'  => $total,
            'metode'       => $metode,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        // Update status order & detail
        $order->detailOrders()->update(['status_detail' => 'selesai']);
        $order->update(['status_order' => 'selesai']);

        // TIDAK langsung bebaskan meja — biarkan pelanggan logout sendiri
        // atau admin kosongkan via halaman meja
        // Meja dibebaskan otomatis saat pelanggan klik Keluar

        return redirect()
            ->route('kasir.struk', $kdTrx)
            ->with('success', 'Pembayaran berhasil!');
    }
}