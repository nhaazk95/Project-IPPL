<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailOrderTemporary extends Model
{
    use HasFactory;

    protected $table = 'detail_order_temporaries';
    protected $primaryKey = 'kd_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_detail',
        'order_kd',
        'pelanggan_kd',   // FIX: was 'user_kd', sesuai kolom di migration
        'menu_kd',
        'total',
        'sub_total',
        'keterangan',
    ];

    protected $casts = [
        'total'     => 'integer',
        'sub_total' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * FIX: keranjang dimiliki Pelanggan (bukan User), via pelanggan_kd
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_kd', 'kd_pelanggan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_kd', 'kd_menu');
    }

    // ==================== METHODS ====================

    public function updateKeranjang(int $jumlahBaru): void
    {
        $menu = Menu::findOrFail($this->menu_kd);
        $this->update([
            'total'     => $jumlahBaru,
            'sub_total' => $menu->harga * $jumlahBaru,
        ]);
    }

    public function checkout(string $kdPelanggan, int $noMeja): Order
    {
        $order = new Order();
        return $order->createOrder($kdPelanggan, $noMeja);
    }
}
