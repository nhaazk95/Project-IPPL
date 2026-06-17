<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function menu_has_fillable_attributes()
    {
        $menu = new Menu();
        $fillable = $menu->getFillable();
        $this->assertEquals([
            'kd_menu', 'name_menu', 'kategori_id', 'harga',
            'description', 'status', 'photo'
        ], $fillable);
    }

    /** @test */
    public function menu_belongs_to_kategori()
    {
        $kategori = Kategori::create([
            'kd_kategori' => 'KAT-001',
            'name_kategori' => 'Test'
        ]);
        $menu = Menu::create([
            'kd_menu' => 'MNU-001',
            'name_menu' => 'Test Menu',
            'kategori_id' => $kategori->kd_kategori,
            'harga' => 10000,
            'status' => 'tersedia'
        ]);
        $this->assertInstanceOf(Kategori::class, $menu->kategori);
        $this->assertEquals($kategori->kd_kategori, $menu->kategori->kd_kategori);
    }
}