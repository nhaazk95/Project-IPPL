<?php

namespace App\Http\Controllers\Admin;

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
        $rows = [];

        if ($request->filled('dari') && $request->filled('sampai')) {
            $dari   = $request->dari;
            $sampai = $request->sampai;

            // Ambil semua detail order dalam periode
            $details = \App\Models\DetailOrder::with(['order.meja', 'menu'])
                ->whereHas('order', function ($q) use ($dari, $sampai) {
                    $q->whereDate('tanggal', '>=', $dari)
                      ->whereDate('tanggal', '<=', $sampai);
                })
                ->get();

            foreach ($details as $d) {
                $rows[] = [
                    'kode_order' => $d->order->kd_order    ?? '-',
                    'pelanggan'  => $d->order->nama_user   ?? 'Tamu',
                    'no_meja'    => $d->order->no_meja     ?? '-',
                    'nama_menu'  => $d->menu->name_menu    ?? '-',
                    'jumlah'     => $d->total,
                    'sub_total'  => $d->sub_total,
                    'harga'      => $d->menu->harga        ?? 0,
                    'tanggal'    => $d->order->tanggal
                        ? \Carbon\Carbon::parse($d->order->tanggal)->format('Y-m-d')
                        : '-',
                ];
            }
        }

        return view('admin.laporan.orderan', compact('rows'));
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
        // Untuk dropdown pencarian
        $semuaTransaksi = Transaksi::orderByDesc('tanggal')->get();

        // Filter jika dipilih salah satu
        $query = Transaksi::with(['order.meja', 'kasir']);
        if ($request->filled('kd_transaksi')) {
            $query->where('kd_transaksi', $request->kd_transaksi);
        }

        $transaksis = $query->orderByDesc('tanggal')->get();

        return view('admin.laporan.transaksi', compact('transaksis', 'semuaTransaksi'));
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
