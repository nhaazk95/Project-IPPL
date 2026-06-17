<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kategori;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['level_id' => 1]);
        $this->kategori = Kategori::create([
            'kd_kategori' => 'KAT-MKNN',
            'name_kategori' => 'Makanan Utama'
        ]);
        Storage::fake('public');
    }

    #[Test]
    public function admin_can_view_menu_index()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/menu');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_store_menu_with_photo()
    {
        // Buat file dummy dengan ekstensi jpg (tidak perlu GD)
        $file = UploadedFile::fake()->create('menu.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->adminUser)->post('/admin/menu', [
            'name_menu' => 'Sop Buntut',
            'kategori_id' => $this->kategori->kd_kategori,
            'harga' => 75000,
            'description' => 'Enak',
            'status' => 'tersedia',
            'photo' => $file
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('menus', ['name_menu' => 'Sop Buntut']);
        Storage::disk('public')->assertExists('menus/' . $file->hashName());
    }

    #[Test]
    public function store_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/menu', []);
        $response->assertSessionHasErrors(['name_menu', 'kategori_id', 'harga', 'status']);
    }

    #[Test]
    public function admin_can_update_menu()
    {
        $menu = \App\Models\Menu::create([
            'kd_menu' => 'MNU-001',
            'name_menu' => 'Old Name',
            'kategori_id' => $this->kategori->kd_kategori,
            'harga' => 50000,
            'status' => 'tersedia'
        ]);
        $response = $this->actingAs($this->adminUser)->put("/admin/menu/{$menu->kd_menu}", [
            'name_menu' => 'Updated Name',
            'kategori_id' => $this->kategori->kd_kategori,
            'harga' => 60000,
            'status' => 'habis'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('menus', ['kd_menu' => 'MNU-001', 'name_menu' => 'Updated Name']);
    }
}