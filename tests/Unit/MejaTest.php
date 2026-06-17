<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Meja;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MejaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function meja_has_fillable_attributes()
    {
        $meja = new Meja();
        $fillable = $meja->getFillable();
        $this->assertEquals(['no_meja', 'user_kd', 'status'], $fillable);
    }

    /** @test */
    public function meja_can_be_created()
    {
        $meja = Meja::create(['no_meja' => 10, 'status' => 'tersedia']);
        $this->assertDatabaseHas('mejas', ['no_meja' => 10, 'status' => 'tersedia']);
    }

    /** @test */
    public function toggleStatus_changes_status()
    {
        $meja = Meja::create(['no_meja' => 5, 'status' => 'tersedia']);
        $meja->setStatus('terisi');
        $this->assertEquals('terisi', $meja->fresh()->status);
    }
}