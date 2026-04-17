<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Level;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Meja;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // LEVELS
        // =========================================
        $admin = Level::firstOrCreate(['id' => 1], ['nama_level' => 'Admin']);
        $kasir = Level::firstOrCreate(['id' => 2], ['nama_level' => 'Kasir']);

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

        // =========================================
        // KATEGORIS
        // =========================================
        $kategoris = [
            ['kd_kategori' => 'KAT-MKNN', 'name_kategori' => 'Makanan Utama',  'description' => 'Menu makanan berat pilihan'],
            ['kd_kategori' => 'KAT-CKTL', 'name_kategori' => 'Camilan',        'description' => 'Snack dan appetizer'],
            ['kd_kategori' => 'KAT-MNMN', 'name_kategori' => 'Minuman',        'description' => 'Minuman segar dan hangat'],
            ['kd_kategori' => 'KAT-DSSRT','name_kategori' => 'Dessert',        'description' => 'Penutup dan makanan manis'],
        ];
        foreach ($kategoris as $k) {
            Kategori::firstOrCreate(['kd_kategori' => $k['kd_kategori']], $k);
        }

        // =========================================
        // MENUS
        // =========================================
        $menus = [
            // Makanan Utama
            ['kd_menu' => 'MNU-NAS01', 'name_menu' => 'Nasi Goreng Spesial',    'kategori_id' => 'KAT-MKNN', 'harga' => 35000,  'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-MIE01', 'name_menu' => 'Mie Goreng Jawa',        'kategori_id' => 'KAT-MKNN', 'harga' => 30000,  'description' => 'Mie goreng khas Jawa dengan bumbu rempah tradisional', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-AYM01', 'name_menu' => 'Ayam Bakar Taliwang',   'kategori_id' => 'KAT-MKNN', 'harga' => 55000,  'description' => 'Ayam bakar pedas khas Lombok dengan bumbu kacang', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-GDG01', 'name_menu' => 'Gado-Gado Spesial',     'kategori_id' => 'KAT-MKNN', 'harga' => 28000,  'description' => 'Sayuran segar dengan bumbu kacang dan lontong', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-SOP01', 'name_menu' => 'Sop Buntut',            'kategori_id' => 'KAT-MKNN', 'harga' => 75000,  'description' => 'Sop buntut sapi dengan kuah bening gurih', 'status' => 'tersedia'],

            // Camilan
            ['kd_menu' => 'MNU-TEM01', 'name_menu' => 'Tempe Mendoan',          'kategori_id' => 'KAT-CKTL', 'harga' => 15000,  'description' => 'Tempe goreng tepung khas Purwokerto', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-PIZ01', 'name_menu' => 'Pizza Mini',             'kategori_id' => 'KAT-CKTL', 'harga' => 40000,  'description' => 'Pizza mini dengan topping keju dan sayuran', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-FRI01', 'name_menu' => 'French Fries',           'kategori_id' => 'KAT-CKTL', 'harga' => 25000,  'description' => 'Kentang goreng renyah dengan saus pilihan', 'status' => 'tersedia'],

            // Minuman
            ['kd_menu' => 'MNU-KPI01', 'name_menu' => 'Kopi Hitam',            'kategori_id' => 'KAT-MNMN', 'harga' => 15000,  'description' => 'Kopi robusta lokal pilihan', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-KPI02', 'name_menu' => 'Kopi Susu Gula Aren',   'kategori_id' => 'KAT-MNMN', 'harga' => 25000,  'description' => 'Espresso dengan susu segar dan gula aren asli', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-JUS01', 'name_menu' => 'Jus Alpukat',           'kategori_id' => 'KAT-MNMN', 'harga' => 22000,  'description' => 'Jus alpukat segar dengan susu dan madu', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-TEH01', 'name_menu' => 'Es Teh Manis',          'kategori_id' => 'KAT-MNMN', 'harga' => 10000,  'description' => 'Teh hitam manis dingin menyegarkan', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-MTH01', 'name_menu' => 'Matcha Latte',          'kategori_id' => 'KAT-MNMN', 'harga' => 32000,  'description' => 'Matcha Jepang premium dengan steamed milk', 'status' => 'tersedia'],

            // Dessert
            ['kd_menu' => 'MNU-PUD01', 'name_menu' => 'Pudding Coklat',        'kategori_id' => 'KAT-DSSRT','harga' => 18000,  'description' => 'Pudding coklat lembut dengan vla vanilla', 'status' => 'tersedia'],
            ['kd_menu' => 'MNU-ICE01', 'name_menu' => 'Es Krim 3 Rasa',        'kategori_id' => 'KAT-DSSRT','harga' => 30000,  'description' => 'Es krim coklat, vanilla, dan stroberi', 'status' => 'tersedia'],
        ];
        foreach ($menus as $m) {
            Menu::firstOrCreate(['kd_menu' => $m['kd_menu']], $m);
        }

        // =========================================
        // MEJA
        // =========================================
        for ($i = 1; $i <= 50; $i++) {
            Meja::firstOrCreate(['no_meja' => $i], [
                'no_meja' => $i,
                'status'  => 'tersedia',
            ]);
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('   Admin  → username: admin   | password: admin123');
        $this->command->info('   Kasir  → username: kasir1  | password: kasir123');
    }
}