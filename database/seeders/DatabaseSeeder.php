<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Level;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // LEVELS
        // =========================================
        Level::firstOrCreate(['id' => 1], ['nama_level' => 'Admin']);
        Level::firstOrCreate(['id' => 2], ['nama_level' => 'Kasir']);

        // =========================================
        // USERS
        // =========================================
        User::firstOrCreate(['username' => 'admin'], [
            'kd_user'  => 'USR-ADMIN01',
            'name'     => 'Administrator',
            'email'    => 'admin@dnusa.id',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'level_id' => 1,
        ]);

        User::firstOrCreate(['username' => 'kasir1'], [
            'kd_user'  => 'USR-KASIR01',
            'name'     => 'Budi Santoso',
            'email'    => 'kasir1@dnusa.id',
            'username' => 'kasir1',
            'password' => Hash::make('kasir123'),
            'level_id' => 2,
        ]);

        User::firstOrCreate(['username' => 'kasir2'], [
            'kd_user'  => 'USR-KASIR02',
            'name'     => 'Siti Rahma',
            'email'    => 'kasir2@dnusa.id',
            'username' => 'kasir2',
            'password' => Hash::make('kasir123'),
            'level_id' => 2,
        ]);
    }
}