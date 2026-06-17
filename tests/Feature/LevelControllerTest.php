<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['level_id' => 1]);
    }

    /** @test */
    public function admin_can_view_levels()
    {
        Level::create(['nama_level' => 'Admin']);
        Level::create(['nama_level' => 'Kasir']);
        $response = $this->actingAs($this->adminUser)->get('/admin/level');
        $response->assertStatus(200);
        $response->assertViewHas('levels');
    }

    /** @test */
    public function admin_can_store_new_level()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/level', [
            'nama_level' => 'Supervisor'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('levels', ['nama_level' => 'Supervisor']);
    }

    /** @test */
    public function store_validates_unique_nama_level()
    {
        Level::create(['nama_level' => 'Manager']);
        $response = $this->actingAs($this->adminUser)->post('/admin/level', [
            'nama_level' => 'Manager'
        ]);
        $response->assertSessionHasErrors('nama_level');
    }

    /** @test */
    public function admin_can_update_level()
    {
        $level = Level::create(['nama_level' => 'Old']);
        $response = $this->actingAs($this->adminUser)->put("/admin/level/{$level->id}", [
            'nama_level' => 'Updated'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('levels', ['id' => $level->id, 'nama_level' => 'Updated']);
    }

    /** @test */
    public function admin_cannot_delete_level_that_has_users()
    {
        $level = Level::create(['nama_level' => 'Kasir']);
        User::factory()->create(['level_id' => $level->id]);
        $response = $this->actingAs($this->adminUser)->delete("/admin/level/{$level->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('levels', ['id' => $level->id]);
    }
}