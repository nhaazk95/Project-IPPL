<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\DetailOrder;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $kasir;
    protected $order;
    protected $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir = User::factory()->create(['level_id' => 2]);

        // Buat meja dan pelanggan
        $meja = Meja::create(['no_meja' => 3, 'status' => 'tersedia']);
        $this->pelanggan = Pelanggan::create([
            'kd_pelanggan' => 'PLG-001',
            'name_pelanggan' => 'Budi',
            'no_meja' => $meja->no_meja,
            'login_at' => now()
        ]);

        // Buat kategori dan menu
        $kategori = Kategori::create(['kd_kategori' => 'KAT-001', 'name_kategori' => 'Makanan']);
        $menu = Menu::create([
            'kd_menu' => 'MNU-001',
            'name_menu' => 'Nasi Goreng',
            'kategori_id' => $kategori->kd_kategori,
            'harga' => 35000,
            'status' => 'tersedia'
        ]);

        // Buat order dengan kd_pelanggan
        $this->order = Order::create([
            'kd_order' => 'ORD-001',
            'no_meja' => $meja->no_meja,
            'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
            'nama_user' => $this->pelanggan->name_pelanggan,
            'tanggal' => now()->toDateString(),
            'waktu' => now(),
            'status_order' => 'pending'
        ]);

        // Buat detail order
        DetailOrder::create([
            'kd_detail' => 'DTL-001',
            'order_kd' => $this->order->kd_order,
            'pelanggan_kd' => $this->pelanggan->kd_pelanggan,
            'menu_kd' => $menu->kd_menu,
            'total' => 1,
            'sub_total' => 35000,
            'status_detail' => 'pending'
        ]);
    }

    #[Test]
    public function kasir_can_view_order_index()
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/order');
        $response->assertStatus(200);
    }

    #[Test]
    public function kasir_can_view_order_detail()
    {
        $response = $this->actingAs($this->kasir)->get("/kasir/order/{$this->order->kd_order}");
        $response->assertStatus(200);
        $response->assertViewHas('order');
    }

    #[Test]
    public function kasir_can_process_cash_payment()
    {
        $response = $this->actingAs($this->kasir)->post("/kasir/order/{$this->order->kd_order}/bayar", [
            'metode' => 'cash',
            'jumlah_bayar' => 50000
        ]);
        $response->assertRedirectContains('/kasir/struk');
        $this->assertDatabaseHas('transaksis', ['order_kd' => $this->order->kd_order]);
        $this->assertEquals('selesai', $this->order->fresh()->status_order);
    }

    #[Test]
    public function kasir_cannot_pay_less_than_total()
    {
        $response = $this->actingAs($this->kasir)->post("/kasir/order/{$this->order->kd_order}/bayar", [
            'metode' => 'cash',
            'jumlah_bayar' => 10000
        ]);
        $response->assertSessionHasErrors('jumlah_bayar');
        $this->assertDatabaseMissing('transaksis', ['order_kd' => $this->order->kd_order]);
    }
}