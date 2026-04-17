<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';
    protected $primaryKey = 'kd_kategori';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_kategori',
        'name_kategori',
        'description',
        'photo',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Kategori memiliki banyak Menu
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_id', 'kd_kategori');
    }

    // ==================== METHODS ====================

    /**
     * Tambah kategori baru
     */
    public function tambah(array $data): void
    {
        self::create($data);
    }

    /**
     * Ubah data kategori
     */
    public function ubah(array $data): void
    {
        $this->update($data);
    }

    /**
     * Hapus kategori
     */
    public function hapus(): void
    {
        $this->delete();
    }
}
