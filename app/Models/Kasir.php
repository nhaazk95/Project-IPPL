<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Kasir extends User
{
    protected static function booted(): void
    {
        static::addGlobalScope('kasir', function (Builder $builder) {
            $builder->where('level_id', 2);
        });
    }

    public function prosesPembayaran(string $kdOrder, float $jumlahBayar): void
    {
        $order = Order::findOrFail($kdOrder);

        Transaksi::create([
            'kd_transaksi' => 'TRX-' . time(),
            'order_kd'     => $order->kd_order,
            'user_kd'      => $this->kd_user,
            'total_harga'  => $order->detailOrders()->sum('sub_total'),
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        $order->updateStatus('selesai');
    }

    public function kembalianCash(float $totalHarga, float $jumlahBayar): float
    {
        return $jumlahBayar - $totalHarga;
    }

    public function cetakStruk(string $kdTransaksi): array
    {
        $transaksi = Transaksi::with(['order.detailOrders.menu'])->findOrFail($kdTransaksi);

        return [
            'kd_transaksi' => $transaksi->kd_transaksi,
            'tanggal'      => $transaksi->tanggal,
            'waktu'        => $transaksi->waktu,
            'total_harga'  => $transaksi->total_harga,
            'items'        => $transaksi->order->detailOrders,
        ];
    }

    public function verifikasiPembayaranQR(string $qrData): bool
    {
        return !empty($qrData);
    }

    public function cetakLaporan(string $tanggal): array
    {
        $transaksis = Transaksi::whereDate('tanggal', $tanggal)
            ->with(['order', 'detailOrders'])
            ->get();

        return [
            'tanggal'          => $tanggal,
            'total_transaksi'  => $transaksis->count(),
            'total_pendapatan' => $transaksis->sum('total_harga'),
            'data'             => $transaksis,
        ];
    }
}
