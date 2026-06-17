<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();
        $this->assertEquals([
            'kd_user', 'name', 'email', 'username',
            'password', 'level_id', 'foto', 'no_hp'
        ], $fillable);
    }

    /** @test */
    public function user_has_hidden_attributes()
    {
        $user = new User();
        $hidden = $user->getHidden();
        $this->assertEquals(['password', 'remember_token'], $hidden);
    }

    /** @test */
    public function isAdmin_returns_true_for_admin_user()
    {
        $admin = User::factory()->create(['level_id' => 1]);
        $this->assertTrue($admin->isAdmin());
    }

    /** @test */
    public function isAdmin_returns_false_for_kasir_user()
    {
        $kasir = User::factory()->create(['level_id' => 2]);
        $this->assertFalse($kasir->isAdmin());
    }

    /** @test */
    public function isKasir_returns_true_for_kasir_user()
    {
        $kasir = User::factory()->create(['level_id' => 2]);
        $this->assertTrue($kasir->isKasir());
    }

    /** @test */
    public function user_belongs_to_level()
    {
        $level = Level::create(['nama_level' => 'Admin']);
        $user = User::factory()->create(['level_id' => $level->id]);
        $this->assertInstanceOf(Level::class, $user->level);
        $this->assertEquals($level->id, $user->level->id);
    }
}