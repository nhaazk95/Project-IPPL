<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\DetailOrderTemporary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class KeranjangControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $pelangganKd;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat meja tersedia
        $meja = Meja::create(['no_meja' => 5, 'status' => 'tersedia']);

        // 2. Login pelanggan secara nyata (akan membuat session)
        $response = $this->post('/pelanggan/masuk', [
            'name_pelanggan' => 'Budi',
            'no_meja'        => $meja->no_meja,
        ]);
        $response->assertRedirect(); // pastikan login sukses

        // 3. Ambil kd_pelanggan dari session (yang dipakai oleh controller)
        $this->pelangganKd = session('pelanggan.kd_pelanggan');
        $this->assertNotNull($this->pelangganKd, 'Session pelanggan tidak terbentuk');

        // 4. Pastikan meja sekarang terisi
        $meja->refresh();
        $this->assertEquals('terisi', $meja->status);

        // 5. Buat menu untuk testing
        $kategori = Kategori::create([
            'kd_kategori'   => 'KAT-001',
            'name_kategori' => 'Makanan',
        ]);
        $this->menu = Menu::create([
            'kd_menu'     => 'MNU-001',
            'name_menu'   => 'Nasi Goreng',
            'kategori_id' => $kategori->kd_kategori,
            'harga'       => 35000,
            'status'      => 'tersedia',
        ]);
    }

    #[Test]
    public function pelanggan_can_add_item_to_cart()
    {
        $response = $this->post('/pelanggan/keranjang/tambah', [
            'kd_menu' => $this->menu->kd_menu,
            'jumlah'  => 2,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('detail_order_temporaries', [
            'pelanggan_kd' => $this->pelangganKd,
            'menu_kd'      => $this->menu->kd_menu,
            'total'        => 2,
        ]);
    }

    #[Test]
    public function pelanggan_can_update_cart_qty()
    {
        // Tambah item dulu
        $this->post('/pelanggan/keranjang/tambah', [
            'kd_menu' => $this->menu->kd_menu,
            'jumlah'  => 1,
        ]);

        $detail = DetailOrderTemporary::where('pelanggan_kd', $this->pelangganKd)->first();
        $this->assertNotNull($detail, 'Detail order temporary tidak ditemukan');

        $response = $this->put("/pelanggan/keranjang/{$detail->kd_detail}", ['aksi' => 'tambah']);
        $response->assertRedirect();
        $this->assertEquals(2, $detail->fresh()->total);
    }

    #[Test]
    public function pelanggan_can_checkout()
    {
        // Tambah item dulu
        $this->post('/pelanggan/keranjang/tambah', [
            'kd_menu' => $this->menu->kd_menu,
            'jumlah'  => 1,
        ]);

        $response = $this->post('/pelanggan/checkout', ['keterangan' => '']);
        $response->assertRedirect('/pelanggan/pembayaran-preview');
    }
}