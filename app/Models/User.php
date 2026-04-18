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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'level_id' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * User dimiliki oleh Level
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    /**
     * User mengelola banyak Meja
     */
    public function mejas()
    {
        return $this->hasMany(Meja::class, 'user_kd', 'kd_user');
    }

    /**
     * User membuat banyak Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_kd', 'kd_user');
    }

    /**
     * User memiliki banyak Transaksi
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'user_kd', 'kd_user');
    }

    /**
     * User memiliki banyak DetailOrderTemporary (keranjang)
     */
    public function detailOrderTemporaries()
    {
        return $this->hasMany(DetailOrderTemporary::class, 'user_kd', 'kd_user');
    }

    // ==================== METHODS ====================

    /**
     * Login user - mengembalikan boolean
     */
    public function login(string $username, string $password): bool
    {
        $user = self::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return true;
        }
        return false;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Register user baru
     */
    public function register(array $data): void
    {
        $data['password'] = Hash::make($data['password']);
        self::create($data);
    }

    /**
     * Ganti password user
     */
    public function changePassword(string $newPassword): void
    {
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    // ==================== HELPER ====================

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return $this->level_id === 1;
    }

    /**
     * Cek apakah user adalah Kasir
     */
    public function isKasir(): bool
    {
        return $this->level_id === 2;
    }
}
