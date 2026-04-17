<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $primaryKey = 'kd_menu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_menu',
        'name_menu',
        'kategori_id',
        'harga',
        'description',
        'status',
        'photo',
    ];

    protected $casts = [
        'harga' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Menu dimiliki oleh Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kd_kategori');
    }

    /**
     * Menu dipilih di banyak DetailOrderTemporary
     */
    public function detailOrderTemporaries()
    {
        return $this->hasMany(DetailOrderTemporary::class, 'menu_kd', 'kd_menu');
    }

    /**
     * Menu ada di banyak DetailOrder
     */
    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'menu_kd', 'kd_menu');
    }

    // ==================== METHODS ====================

    /**
     * Tambah menu baru
     */
    public function tambah(array $data): void
    {
        self::create($data);
    }

    /**
     * Ubah data menu
     */
    public function ubah(array $data): void
    {
        $this->update($data);
    }

    /**
     * Hapus menu
     */
    public function hapus(): void
    {
        $this->delete();
    }
}
