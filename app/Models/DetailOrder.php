<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailOrder extends Model
{
    use HasFactory;

    protected $table = 'detail_orders';
    protected $primaryKey = 'kd_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_detail',
        'order_kd',
        'pelanggan_kd',   // FIX: was 'user_kd', kolom di DB adalah pelanggan_kd
        'menu_kd',
        'transaksi_kd',
        'total',
        'sub_total',
        'keterangan',
        'status_detail',
    ];

    protected $casts = [
        'total'     => 'integer',
        'sub_total' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_kd', 'kd_order');
    }

    public function pelanggan()
    {
        // FIX: relasi ke Pelanggan (bukan User), via pelanggan_kd
        return $this->belongsTo(Pelanggan::class, 'pelanggan_kd', 'kd_pelanggan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_kd', 'kd_menu');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_kd', 'kd_transaksi');
    }

    public function updateStatus(string $status): void
    {
        $this->status_detail = $status;
        $this->save();
    }
}
