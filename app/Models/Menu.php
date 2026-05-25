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

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kd_kategori');
    }

    public function detailOrderTemporaries()
    {
        return $this->hasMany(DetailOrderTemporary::class, 'menu_kd', 'kd_menu');
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'menu_kd', 'kd_menu');
    }

    public function tambah(array $data): void
    {
        self::create($data);
    }

    public function ubah(array $data): void
    {
        $this->update($data);
    }

    public function hapus(): void
    {
        $this->delete();
    }
}
