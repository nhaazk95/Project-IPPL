<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class LevelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function level_has_fillable_attributes()
    {
        $level = new Level();
        $fillable = $level->getFillable();
        // Hanya 'nama_level' karena 'id' tidak boleh fillable
        $this->assertEquals(['nama_level'], $fillable);
    }

    #[Test]
    public function level_can_be_created()
    {
        $level = Level::create(['nama_level' => 'Supervisor']);
        $this->assertDatabaseHas('levels', ['nama_level' => 'Supervisor']);
    }

    #[Test]
    public function level_has_many_users()
    {
        $level = Level::create(['nama_level' => 'Kasir']);
        // Gunakan User factory yang sudah dibuat sebelumnya
        User::factory()->create(['level_id' => $level->id]);
        User::factory()->create(['level_id' => $level->id]);
        $this->assertCount(2, $level->users);
        $this->assertInstanceOf(User::class, $level->users->first());
    }
}