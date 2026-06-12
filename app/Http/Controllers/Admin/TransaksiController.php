<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['meja', 'detailOrders.menu'])
            ->whereIn('status_order', ['pending', 'diproses', 'siap'])
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

    public function bayar(Request $request, string $kd_order)
    {
        $request->validate([
            'metode'       => 'required|in:cash,debit,qris',
            'jumlah_bayar' => 'nullable|integer|min:0',
        ]);

        $order = Order::with('detailOrders')->findOrFail($kd_order);

        if ($order->transaksi) {
            return redirect()
                ->route('admin.struk', $order->transaksi->kd_transaksi)
                ->with('info', 'Order ini sudah dibayar.');
        }

        $total  = $order->detailOrders->sum('sub_total');
        $metode = $request->metode;

        if ($metode === 'cash') {
            $jumlahBayar = (int) $request->jumlah_bayar;
            if ($jumlahBayar < $total) {
                return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar kurang dari total.']);
            }
            session(['jumlah_bayar' => $jumlahBayar]);
        } else {
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

        $order->detailOrders()->update(['status_detail' => 'selesai']);
        $order->update(['status_order' => 'selesai']);

        // Tidak langsung bebaskan meja — biarkan pelanggan lihat nota dulu
        // Meja dibebaskan saat pelanggan logout

        return redirect()
            ->route('admin.struk', $kdTrx)
            ->with('success', 'Pembayaran berhasil!');
    }

    public function struk(string $kd_transaksi)
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);

        return view('kasir.struk', compact('transaksi'));
    }

    public function show(string $kd_transaksi)
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu', 'order.meja', 'kasir'])
            ->findOrFail($kd_transaksi);

        return view('admin.transaksi.show', compact('transaksi'));
    }
}