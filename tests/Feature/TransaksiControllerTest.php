<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Meja;
use App\Models\Pelanggan;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $kasir;
    protected $order;
    protected $transaksi;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Kasir
        $this->kasir = User::factory()->create(['level_id' => 2]);

        // 2. Meja
        $meja = Meja::create(['no_meja' => 5, 'status' => 'tersedia']);

        // 3. Pelanggan
        $pelanggan = Pelanggan::create([
            'kd_pelanggan'   => 'PLG-001',
            'name_pelanggan' => 'Budi',
            'no_meja'        => $meja->no_meja,
            'login_at'       => now(),
        ]);

        // 4. Order dengan kd_pelanggan
        $this->order = Order::create([
            'kd_order'     => 'ORD-001',
            'no_meja'      => $meja->no_meja,
            'kd_pelanggan' => $pelanggan->kd_pelanggan,
            'nama_user'    => $pelanggan->name_pelanggan,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
            'status_order' => 'pending',
        ]);

        // 5. Transaksi
        $this->transaksi = Transaksi::create([
            'kd_transaksi' => 'TRX-001',
            'order_kd'     => $this->order->kd_order,
            'user_kd'      => $this->kasir->kd_user,
            'total_harga'  => 50000,
            'metode'       => 'cash',
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);
    }

    #[Test]
    public function kasir_can_view_transaksi_index()
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/transaksi');
        $response->assertStatus(200);
    }

    #[Test]
    public function kasir_can_view_laporan()
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/laporan');
        $response->assertStatus(200);
        $response->assertViewHas('transaksis');
    }

    #[Test]
    public function kasir_can_view_struk()
    {
        $response = $this->actingAs($this->kasir)->get("/kasir/struk/{$this->transaksi->kd_transaksi}");
        $response->assertStatus(200);
        $response->assertViewHas('transaksi');
    }

    #[Test]
    public function laporan_filters_by_kd_transaksi()
    {
        // Buat transaksi kedua dengan order berbeda
        $meja2 = Meja::create(['no_meja' => 6, 'status' => 'tersedia']);
        $pelanggan2 = Pelanggan::create([
            'kd_pelanggan'   => 'PLG-002',
            'name_pelanggan' => 'Ani',
            'no_meja'        => $meja2->no_meja,
            'login_at'       => now(),
        ]);
        $order2 = Order::create([
            'kd_order'     => 'ORD-002',
            'no_meja'      => $meja2->no_meja,
            'kd_pelanggan' => $pelanggan2->kd_pelanggan,
            'nama_user'    => $pelanggan2->name_pelanggan,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
            'status_order' => 'selesai',
        ]);
        Transaksi::create([
            'kd_transaksi' => 'TRX-002',
            'order_kd'     => $order2->kd_order,
            'user_kd'      => $this->kasir->kd_user,
            'total_harga'  => 70000,
            'metode'       => 'debit',
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
        ]);

        $response = $this->actingAs($this->kasir)
            ->get("/kasir/laporan?kd_transaksi={$this->transaksi->kd_transaksi}");

        $response->assertStatus(200);
        $transaksis = $response->viewData('transaksis');
        $this->assertCount(1, $transaksis);
        $this->assertEquals($this->transaksi->kd_transaksi, $transaksis->first()->kd_transaksi);
    }
}