<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory;

    protected $table = 'levels';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nama_level',
    ];

    // ==================== RELATIONSHIPS ====================

    public function users()
    {
        return $this->hasMany(User::class, 'level_id', 'id');
    }

    public function mejas()
    {
        return $this->hasMany(Meja::class, 'level_id', 'id');
    }
}