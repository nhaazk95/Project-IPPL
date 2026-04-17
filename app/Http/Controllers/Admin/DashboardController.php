<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Order;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\DetailOrder;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalTransaksiHari = Transaksi::whereDate('tanggal', $today)->count();
        $pendapatanHari     = Transaksi::whereDate('tanggal', $today)->sum('total_harga');
        $orderPending       = Order::where('status_order', 'pending')->count();

        $mejas     = Meja::orderBy('no_meja')->get();
        $totalMeja = $mejas->count();
        $mejaAktif = $mejas->where('status', 'terisi')->count();

        $recentTransaksi = Transaksi::whereDate('tanggal', $today)
            ->latest('waktu')
            ->limit(5)
            ->get();

        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->isoFormat('ddd');
            $chartData[]   = Transaksi::whereDate('tanggal', $date)->sum('total_harga');
        }

        $topMenus = Menu::withCount(['detailOrders as total_terjual' => function ($q) {
            $q->whereMonth('created_at', now()->month);
        }])
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalTransaksiHari', 'pendapatanHari', 'orderPending',
            'mejas', 'totalMeja', 'mejaAktif',
            'recentTransaksi', 'chartLabels', 'chartData', 'topMenus'
        ));
    }
}