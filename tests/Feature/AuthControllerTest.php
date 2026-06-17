<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_login_with_valid_credentials()
    {
        $admin = User::factory()->create([
            'level_id' => 1,
            'password' => bcrypt('admin123'),
            'username' => 'admin'
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    #[Test]
    public function kasir_can_login_with_valid_credentials()
    {
        $kasir = User::factory()->create([
            'level_id' => 2,
            'password' => bcrypt('kasir123'),
            'username' => 'kasir1'
        ]);

        $response = $this->post('/login', [
            'username' => 'kasir1',
            'password' => 'kasir123',
        ]);

        $response->assertRedirect('/kasir/dashboard');
        $this->assertAuthenticatedAs($kasir);
    }

    #[Test]
    public function login_fails_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correctpass'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'wrongpass',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    #[Test]
    public function authenticated_user_cannot_access_login_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/');
    }

    #[Test]
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        // Route logout menggunakan POST, bukan GET
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}