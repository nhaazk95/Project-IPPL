<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Pelanggan;
use App\Models\Meja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PelangganTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pelanggan_has_fillable_attributes()
    {
        $pelanggan = new Pelanggan();
        $fillable = $pelanggan->getFillable();
        $this->assertEquals([
            'kd_pelanggan',
            'name_pelanggan',
            'no_meja',
            'login_at'
        ], $fillable);
    }

    #[Test]
    public function pelanggan_can_be_created()
    {
        // Buat meja dulu karena foreign key
        $meja = Meja::create(['no_meja' => 3, 'status' => 'tersedia']);
        
        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => 'PLG-001',
            'name_pelanggan' => 'Budi',
            'no_meja' => $meja->no_meja,
            'login_at' => now()
        ]);
        $this->assertDatabaseHas('pelanggans', ['kd_pelanggan' => 'PLG-001']);
    }
}