<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Meja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MejaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['level_id' => 1]);
    }

    #[Test]
    public function admin_can_add_new_meja()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/meja', [
            'no_meja' => 51,
            'status'  => 'tersedia'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('mejas', ['no_meja' => 51]);
    }

    #[Test]
    public function cannot_add_duplicate_no_meja()
    {
        Meja::create(['no_meja' => 10, 'status' => 'tersedia']);
        $response = $this->actingAs($this->adminUser)->post('/admin/meja', [
            'no_meja' => 10,
            'status'  => 'tersedia'
        ]);
        $response->assertSessionHasErrors('no_meja');
    }

    #[Test]
    public function admin_can_update_meja()
    {
        $meja = Meja::create(['no_meja' => 20, 'status' => 'tersedia']);
        $response = $this->actingAs($this->adminUser)->put("/admin/meja/{$meja->id}", [
            'no_meja' => 21,
            'status'  => 'terisi'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('mejas', ['id' => $meja->id, 'no_meja' => 21, 'status' => 'terisi']);
    }

    #[Test]
    public function admin_can_delete_meja()
    {
        $meja = Meja::create(['no_meja' => 30, 'status' => 'tersedia']);
        $response = $this->actingAs($this->adminUser)->delete("/admin/meja/{$meja->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('mejas', ['id' => $meja->id]);
    }

    // Sesuai dengan controller yang mengizinkan hapus meja terisi
    #[Test]
    public function admin_can_delete_meja_yang_terisi()
    {
        $meja = Meja::create(['no_meja' => 31, 'status' => 'terisi']);
        $response = $this->actingAs($this->adminUser)->delete("/admin/meja/{$meja->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('mejas', ['id' => $meja->id]);
    }
}