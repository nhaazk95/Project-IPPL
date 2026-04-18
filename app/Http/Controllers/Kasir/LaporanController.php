<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Order;
use App\Models\DetailOrder;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function orderan(Request $request)
    {
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $transaksis = Transaksi::with(['order.meja', 'kasir'])
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->orderByDesc('tanggal')
            ->get();

        $totalPendapatan  = $transaksis->sum('total_harga');
        $totalTransaksi   = $transaksis->count();
        $rataRata         = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        $topMenus = Menu::withCount(['detailOrders as total_terjual' => function ($q) use ($dari, $sampai) {
            $q->whereHas('order', function ($o) use ($dari, $sampai) {
                $o->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai);
            });
        }])
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $perHari = Transaksi::selectRaw('DATE(tanggal) as tgl, SUM(total_harga) as total')
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        return view('admin.laporan.orderan', compact(
            'transaksis', 'totalPendapatan', 'totalTransaksi', 'rataRata',
            'topMenus', 'dari', 'sampai', 'perHari'
        ));
    }

    public function exportOrderan(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $transaksis = Transaksi::with(['order.meja', 'kasir', 'order.detailOrders.menu'])
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->orderByDesc('tanggal')
            ->get();

        $filename = 'laporan_orderan_' . $dari . '_sd_' . $sampai . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transaksis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Transaksi', 'No. Meja', 'Kasir', 'Tanggal', 'Waktu', 'Total Harga']);
            foreach ($transaksis as $t) {
                fputcsv($file, [
                    $t->kd_transaksi,
                    $t->order->no_meja ?? '-',
                    $t->kasir->name ?? '-',
                    $t->tanggal,
                    $t->waktu?->format('H:i'),
                    $t->total_harga,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function transaksi(Request $request)
    {
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $transaksis = Transaksi::with(['order.meja', 'kasir', 'detailOrders.menu'])
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->orderByDesc('tanggal')
            ->paginate(20);

        $totalPendapatan = Transaksi::whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->sum('total_harga');

        $totalTransaksi = Transaksi::whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->count();

        return view('admin.laporan.transaksi', compact(
            'transaksis', 'totalPendapatan', 'totalTransaksi', 'dari', 'sampai'
        ));
    }

    public function exportTransaksi(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $transaksis = Transaksi::with(['order.meja', 'kasir', 'detailOrders.menu'])
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->orderByDesc('tanggal')
            ->get();

        $filename = 'laporan_transaksi_' . $dari . '_sd_' . $sampai . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transaksis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Transaksi', 'No. Meja', 'Nama Pelanggan', 'Kasir', 'Tanggal', 'Waktu', 'Total Harga', 'Item']);
            foreach ($transaksis as $t) {
                $items = $t->detailOrders->map(fn($d) => ($d->menu->name_menu ?? '-') . ' x' . $d->total)->implode(', ');
                fputcsv($file, [
                    $t->kd_transaksi,
                    $t->order->no_meja ?? '-',
                    $t->order->nama_user ?? '-',
                    $t->kasir->name ?? '-',
                    $t->tanggal,
                    $t->waktu?->format('H:i'),
                    $t->total_harga,
                    $items,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}