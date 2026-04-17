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
        'tanggal',
        'waktu',
    ];

    protected $casts = [
        'total_harga' => 'integer',
        'tanggal'     => 'date',
        'waktu'       => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Transaksi berasal dari satu Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_kd', 'kd_order');
    }

    /**
     * Transaksi diproses oleh User (Kasir)
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_kd', 'kd_user');
    }

    /**
     * Transaksi memiliki banyak DetailOrder
     */
    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'transaksi_kd', 'kd_transaksi');
    }

    // ==================== METHODS ====================

    /**
     * Hitung total harga dari detail order
     */
    public function hitungTotal(): int
    {
        return $this->detailOrders()->sum('sub_total');
    }

    /**
     * Proses pembayaran dan simpan transaksi
     */
    public function prosesPembayaran(string $kdOrder, string $kdKasir): self
    {
        $order = Order::with('detailOrders')->findOrFail($kdOrder);
        $total = $order->detailOrders->sum('sub_total');

        $transaksi = self::create([
            'kd_transaksi' => 'TRX-' . time(),
            'order_kd'     => $kdOrder,
            'user_kd'      => $kdKasir,
            'total_harga'  => $total,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        // Update status detail order
        $order->detailOrders()->update([
            'transaksi_kd'  => $transaksi->kd_transaksi,
            'status_detail' => 'selesai',
        ]);

        $order->updateStatus('selesai');

        return $transaksi;
    }

    /**
     * Hitung kembalian cash
     */
    public function kembalianCash(int $jumlahBayar): int
    {
        return $jumlahBayar - $this->total_harga;
    }

    /**
     * Cetak struk transaksi
     */
    public function cetakStruk(): array
    {
        return [
            'kd_transaksi' => $this->kd_transaksi,
            'tanggal'      => $this->tanggal,
            'waktu'        => $this->waktu,
            'kasir'        => $this->kasir?->name,
            'total_harga'  => $this->total_harga,
            'items'        => $this->detailOrders()->with('menu')->get(),
        ];
    }
}
