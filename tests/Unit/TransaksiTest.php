<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Transaksi;
use App\Models\Order;
use App\Models\User;
use App\Models\Meja;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function transaksi_has_fillable_attributes()
    {
        $transaksi = new Transaksi();
        $fillable = $transaksi->getFillable();
        // Urutan harus sama dengan yang ada di model Transaksi
        $this->assertEquals([
            'kd_transaksi',
            'order_kd',
            'user_kd',
            'total_harga',
            'metode',
            'tanggal',
            'waktu'
        ], $fillable);
    }

    #[Test]
    public function transaksi_belongs_to_order()
    {
        // Buat meja
        $meja = Meja::create(['no_meja' => 1, 'status' => 'tersedia']);
        // Buat pelanggan
        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => 'PLG-001',
            'name_pelanggan' => 'Budi',
            'no_meja' => $meja->no_meja,
            'login_at' => now()
        ]);
        // Buat order dengan kd_pelanggan
        $order = Order::create([
            'kd_order' => 'ORD-001',
            'no_meja' => $meja->no_meja,
            'kd_pelanggan' => $pelanggan->kd_pelanggan,
            'nama_user' => $pelanggan->name_pelanggan,
            'tanggal' => now()->toDateString(),
            'waktu' => now(),
            'status_order' => 'pending'
        ]);
        // Buat user kasir (factory)
        $kasir = User::factory()->create(['level_id' => 2]);
        // Buat transaksi
        $transaksi = Transaksi::create([
            'kd_transaksi' => 'TRX-001',
            'order_kd' => $order->kd_order,
            'user_kd' => $kasir->kd_user,
            'total_harga' => 50000,
            'metode' => 'cash',
            'tanggal' => now()->toDateString(),
            'waktu' => now()
        ]);
        $this->assertInstanceOf(Order::class, $transaksi->order);
        $this->assertEquals($order->kd_order, $transaksi->order->kd_order);
    }
}