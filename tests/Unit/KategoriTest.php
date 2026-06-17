<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function kategori_has_fillable_attributes()
    {
        $kategori = new Kategori();
        $fillable = $kategori->getFillable();
        $this->assertEquals(['kd_kategori', 'name_kategori', 'description', 'photo'], $fillable);
    }

    /** @test */
    public function kategori_has_many_menus()
    {
        $kategori = Kategori::create([
            'kd_kategori' => 'KAT-MKNN',
            'name_kategori' => 'Makanan Utama'
        ]);
        $menu1 = Menu::create([
            'kd_menu' => 'MNU-001',
            'name_menu' => 'Nasi Goreng',
            'kategori_id' => $kategori->kd_kategori,
            'harga' => 35000,
            'status' => 'tersedia'
        ]);
        $menu2 = Menu::create([
            'kd_menu' => 'MNU-002',
            'name_menu' => 'Mie Goreng',
            'kategori_id' => $kategori->kd_kategori,
            'harga' => 30000,
            'status' => 'tersedia'
        ]);
        $this->assertCount(2, $kategori->menus);
    }
}