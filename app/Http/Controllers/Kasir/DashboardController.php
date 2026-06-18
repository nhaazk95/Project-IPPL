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
        // last_id sekarang berisi timestamp ISO (waktu order terakhir yang sudah
        // diketahui kasir), bukan kd_order — karena kd_order punya suffix acak
        // sehingga perbandingan '>' pada string tidak merepresentasikan urutan waktu.
        $lastSeenAt = $request->input('last_id', '');

        $jumlahPending = Order::where('status_order', 'pending')->count();

        $newOrders = collect();
        if ($lastSeenAt) {
            $newOrders = Order::where('status_order', 'pending')
                ->where('waktu', '>', $lastSeenAt)
                ->orderBy('waktu')
                ->limit(10)
                ->get(['kd_order', 'waktu']);
        }

        $latestOrder = Order::orderByDesc('waktu')->first();

        return response()->json([
            'pending'    => $jumlahPending,
            'new_orders' => $newOrders->count(),
            'orders'     => $newOrders->map(fn ($o) => [
                'kd_order' => $o->kd_order,
                'waktu'    => $o->waktu?->format('H:i'),
            ]),
            // dikembalikan supaya client menyimpan timestamp terbaru sebagai acuan polling berikutnya
            'latest_seen_at' => $latestOrder?->waktu?->toIso8601String() ?? $lastSeenAt,
        ]);
    }
}