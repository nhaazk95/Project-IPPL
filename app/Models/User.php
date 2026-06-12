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
        'kd_user', 'name', 'email', 'username',
        'password', 'level_id', 'foto', 'no_hp',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['level_id' => 'integer'];

    public function level()        { return $this->belongsTo(Level::class, 'level_id', 'id'); }
    public function orders()       { return $this->hasMany(Order::class, 'user_kd', 'kd_user'); }
    public function transaksis()   { return $this->hasMany(Transaksi::class, 'user_kd', 'kd_user'); }

    public function login(string $username, string $password): bool
    {
        $user = self::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return true;
        }
        return false;
    }

    public function isAdmin(): bool { return strtolower($this->level->nama_level ?? '') === 'admin'; }
    public function isKasir(): bool { return strtolower($this->level->nama_level ?? '') === 'kasir'; }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return '';
    }
}