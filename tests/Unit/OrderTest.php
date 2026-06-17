<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Meja;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function order_has_fillable_attributes()
    {
        $order = new Order();
        $fillable = $order->getFillable();
        $this->assertEquals([
            'kd_order', 'no_meja', 'kd_pelanggan', 'nama_user',
            'tanggal', 'waktu', 'keterangan', 'status_order'
        ], $fillable);
    }

    #[Test]
    public function order_belongs_to_meja()
    {
        $meja = Meja::create(['no_meja' => 3, 'status' => 'tersedia']);
        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => 'PLG-001',
            'name_pelanggan' => 'Budi',
            'no_meja' => $meja->no_meja,
            'login_at' => now()
        ]);
        $order = Order::create([
            'kd_order' => 'ORD-001',
            'no_meja' => $meja->no_meja,
            'kd_pelanggan' => $pelanggan->kd_pelanggan,
            'tanggal' => now()->toDateString(),
            'waktu' => now(),
            'status_order' => 'pending'
        ]);
        $this->assertInstanceOf(Meja::class, $order->meja);
    }

    #[Test]
    public function order_can_be_updated_status()
    {
        $meja = Meja::create(['no_meja' => 1, 'status' => 'tersedia']);
        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => 'PLG-002',
            'name_pelanggan' => 'Ani',
            'no_meja' => $meja->no_meja,
            'login_at' => now()
        ]);
        $order = Order::create([
            'kd_order' => 'ORD-002',
            'no_meja' => $meja->no_meja,
            'kd_pelanggan' => $pelanggan->kd_pelanggan,
            'tanggal' => now()->toDateString(),
            'waktu' => now(),
            'status_order' => 'pending'
        ]);
        $order->updateStatus('selesai');
        $this->assertEquals('selesai', $order->fresh()->status_order);
    }
}