<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';
    protected $primaryKey = 'kd_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_transaksi',
        'order_kd',
        'user_kd',
        'total_harga',
        'metode',           // ← TAMBAHKAN INI
        'tanggal',
        'waktu',
    ];

    protected $casts = [
        'total_harga' => 'integer',
        'tanggal'     => 'date',
        'waktu'       => 'datetime',
    ];

    // Relasi
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_kd', 'kd_order');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_kd', 'kd_user');
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'transaksi_kd', 'kd_transaksi');
    }

    // Business methods
    public function hitungTotal(): int
    {
        return $this->detailOrders()->sum('sub_total');
    }

    public function prosesPembayaran(string $kdOrder, string $kdKasir, string $metode = 'cash'): self
    {
        $order = Order::with('detailOrders')->findOrFail($kdOrder);
        $total = $order->detailOrders->sum('sub_total');

        $transaksi = self::create([
            'kd_transaksi' => 'TRX-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'order_kd'     => $kdOrder,
            'user_kd'      => $kdKasir,
            'total_harga'  => $total,
            'metode'       => $metode,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        $order->detailOrders()->update([
            'transaksi_kd'  => $transaksi->kd_transaksi,
            'status_detail' => 'selesai',
        ]);

        $order->updateStatus('selesai');

        return $transaksi;
    }

    public function kembalianCash(int $jumlahBayar): int
    {
        return $jumlahBayar - $this->total_harga;
    }

    public function cetakStruk(): array
    {
        return [
            'kd_transaksi' => $this->kd_transaksi,
            'tanggal'      => $this->tanggal,
            'waktu'        => $this->waktu,
            'kasir'        => $this->kasir?->name,
            'total_harga'  => $this->total_harga,
            'metode'       => $this->metode,
            'items'        => $this->detailOrders()->with('menu')->get(),
        ];
    }
}