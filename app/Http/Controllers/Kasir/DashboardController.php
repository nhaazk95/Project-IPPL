<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\Meja;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $orderPending   = Order::where('status_order', 'pending')->count();
        $orderDiproses  = Order::where('status_order', 'diproses')->count();
        $transaksiHari  = Transaksi::whereDate('tanggal', $today)->where('user_kd', auth()->user()->kd_user)->count();
        $pendapatanHari = Transaksi::whereDate('tanggal', $today)->where('user_kd', auth()->user()->kd_user)->sum('total_harga');

        // Tampilkan semua order aktif: pending, diproses, siap (belum bayar)
        $orderTerbaru = Order::with(['detailOrders.menu', 'transaksi'])
            ->whereIn('status_order', ['pending', 'diproses', 'siap'])
            ->orderByDesc('waktu')
            ->limit(20)
            ->get();

        $mejas = Meja::orderBy('no_meja')->get();

        return view('kasir.dashboard', compact(
            'orderPending', 'orderDiproses', 'transaksiHari', 'pendapatanHari',
            'orderTerbaru', 'mejas'
        ));
    }

    public function notifOrder(Request $request)
    {
        // Pendekatan: kasir mengirim kode-kode order pending yang SUDAH dia ketahui
        // (known_ids, dipisah koma) dan flag initialized (1 setelah polling pertama).
        // Server tinggal cari order pending yang TIDAK ada di daftar itu — jadi tidak
        // bergantung pada perbandingan string kd_order atau presisi timestamp.
        $knownIds   = array_filter(explode(',', $request->input('known_ids', '')));
        $initialized = $request->boolean('initialized');

        $pendingOrders = Order::where('status_order', 'pending')
            ->orderBy('waktu')
            ->get(['kd_order', 'waktu']);

        $newOrders = $initialized
            ? $pendingOrders->reject(fn ($o) => in_array($o->kd_order, $knownIds, true))
            : collect();

        return response()->json([
            'pending'         => $pendingOrders->count(),
            'new_orders'      => $newOrders->count(),
            'orders'          => $newOrders->values()->map(fn ($o) => [
                'kd_order' => $o->kd_order,
                'waktu'    => $o->waktu?->format('H:i'),
            ]),
            // semua kd_order pending saat ini — disimpan client sebagai "known_ids" untuk polling berikutnya
            'all_pending_ids' => $pendingOrders->pluck('kd_order'),
        ]);
    }
}