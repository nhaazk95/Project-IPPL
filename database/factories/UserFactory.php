<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'kd_user' => 'USR-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password123'),
            'level_id' => 2, // default kasir
            'remember_token' => Str::random(10),
        ];
    }

    // State untuk admin (level_id = 1)
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => 1,
        ]);
    }

    // State untuk kasir (level_id = 2)
    public function kasir(): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => 2,
        ]);
    }
}