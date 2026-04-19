<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'kd_user';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_user',
        'name',
        'email',
        'username',
        'password',
        'level_id',    // ← WAJIB ada, sebelumnya tidak ada sehingga level tidak tersimpan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'level_id' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public function mejas()
    {
        return $this->hasMany(Meja::class, 'user_kd', 'kd_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_kd', 'kd_user');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'user_kd', 'kd_user');
    }

    public function detailOrderTemporaries()
    {
        return $this->hasMany(DetailOrderTemporary::class, 'user_kd', 'kd_user');
    }

    // ==================== METHODS ====================

    public function login(string $username, string $password): bool
    {
        $user = self::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function register(array $data): void
    {
        $data['password'] = Hash::make($data['password']);
        self::create($data);
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    // ==================== HELPER ====================

    /**
     * Cek apakah user adalah Admin
     * Pakai nama level (case-insensitive), bukan hardcode ID
     * karena ID level bisa berbeda tiap instalasi
     */
    public function isAdmin(): bool
    {
        return strtolower($this->level->nama_level ?? '') === 'admin';
    }

    /**
     * Cek apakah user adalah Kasir
     */
    public function isKasir(): bool
    {
        return strtolower($this->level->nama_level ?? '') === 'kasir';
    }
}