<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KategoriControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['level_id' => 1]);
    }

    /** @test */
    public function admin_can_view_kategori()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/kategori');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_store_kategori()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/kategori', [
            'name_kategori' => 'Minuman Segar',
            'description' => 'Minuman dingin'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kategoris', ['name_kategori' => 'Minuman Segar']);
    }

    /** @test */
    public function admin_can_update_kategori()
    {
        $kategori = Kategori::create([
            'kd_kategori' => 'KAT-001',
            'name_kategori' => 'Old Name'
        ]);
        $response = $this->actingAs($this->adminUser)->put("/admin/kategori/{$kategori->kd_kategori}", [
            'name_kategori' => 'New Name'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kategoris', ['kd_kategori' => 'KAT-001', 'name_kategori' => 'New Name']);
    }

    /** @test */
    public function admin_cannot_delete_kategori_that_has_menus()
    {
        $kategori = Kategori::create([
            'kd_kategori' => 'KAT-MKNN',
            'name_kategori' => 'Makanan Utama'
        ]);
        // Create menu with this kategori
        \App\Models\Menu::create([
            'kd_menu' => 'MNU-001',
            'name_menu' => 'Nasi Goreng',
            'kategori_id' => $kategori->kd_kategori,
            'harga' => 35000,
            'status' => 'tersedia'
        ]);
        $response = $this->actingAs($this->adminUser)->delete("/admin/kategori/{$kategori->kd_kategori}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kategoris', ['kd_kategori' => 'KAT-MKNN']);
    }
}