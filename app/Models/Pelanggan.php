<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $primaryKey = 'kd_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_pelanggan',
        'name_pelanggan',
        'no_meja',
    ];

    protected $casts = [
        'no_meja' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function orders()
    {
        return $this->hasMany(Order::class, 'kd_pelanggan', 'kd_pelanggan');
    }

    /**
     * FIX: keranjang via pelanggan_kd (bukan user_kd)
     */
    public function keranjang()
    {
        return $this->hasMany(DetailOrderTemporary::class, 'pelanggan_kd', 'kd_pelanggan');
    }

    // ==================== METHODS ====================

    public function loginSementara(string $namaPelanggan, int $noMeja): self
    {
        return self::create([
            'kd_pelanggan'   => 'PLG-' . time(),
            'name_pelanggan' => $namaPelanggan,
            'no_meja'        => $noMeja,
        ]);
    }

    public function logout(): void
    {
        session()->forget('pelanggan');
    }

    public function pesanMenu(string $kdMenu, int $jumlah): void
    {
        $menu = Menu::findOrFail($kdMenu);

        DetailOrderTemporary::create([
            'kd_detail'    => 'TMP-' . time(),
            'pelanggan_kd' => $this->kd_pelanggan,  // FIX: was user_kd
            'menu_kd'      => $kdMenu,
            'total'        => $jumlah,
            'sub_total'    => $menu->harga * $jumlah,
        ]);
    }

    public function tambahKeranjang(string $kdMenu, int $jumlah): void
    {
        $this->pesanMenu($kdMenu, $jumlah);
    }

    public function ubahPesanan(string $kdDetail, int $jumlahBaru): void
    {
        $detail = DetailOrderTemporary::findOrFail($kdDetail);
        $menu   = Menu::findOrFail($detail->menu_kd);

        $detail->update([
            'total'     => $jumlahBaru,
            'sub_total' => $menu->harga * $jumlahBaru,
        ]);
    }

    public function hapusPesanan(string $kdDetail): void
    {
        DetailOrderTemporary::findOrFail($kdDetail)->delete();
    }

    public function lihatNota(string $kdOrder): array
    {
        $order = Order::with('detailOrders.menu')->findOrFail($kdOrder);

        return [
            'kd_order' => $order->kd_order,
            'no_meja'  => $order->no_meja,
            'tanggal'  => $order->tanggal,
            'items'    => $order->detailOrders,
            'total'    => $order->detailOrders->sum('sub_total'),
        ];
    }
}
