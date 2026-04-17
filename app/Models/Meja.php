<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meja extends Model
{
    use HasFactory;

    protected $table = 'mejas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_meja',
        'user_kd',
        'status',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Meja digunakan oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_kd', 'kd_user');
    }

    /**
     * Meja memiliki banyak Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'no_meja', 'no_meja');
    }

    // ==================== METHODS ====================

    /**
     * Set status meja (tersedia / tidak)
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->save();
    }

    /**
     * Tambah meja baru
     */
    public function tambah(array $data): void
    {
        self::create($data);
    }

    /**
     * Ubah data meja
     */
    public function ubah(array $data): void
    {
        $this->update($data);
    }

    /**
     * Hapus meja
     */
    public function hapus(): void
    {
        $this->delete();
    }
}
