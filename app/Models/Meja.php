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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_kd', 'kd_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'no_meja', 'no_meja');
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->save();
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
