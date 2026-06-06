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
        $lastId  = $request->input('last_id', '');
        $jumlah  = Order::where('status_order', 'pending')->count();

        $orderBaru = 0;
        if ($lastId) {
            $orderBaru = Order::where('status_order', 'pending')
                ->where('kd_order', '>', $lastId)
                ->count();
        }

        $latestOrder = Order::where('status_order', 'pending')
            ->orderByDesc('kd_order')->first();

        return response()->json([
            'pending'    => $jumlah,
            'new_orders' => $orderBaru,
            'latest_kd'  => $latestOrder?->kd_order ?? $lastId,
        ]);
    }
}