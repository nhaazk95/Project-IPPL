<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'kd_order';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_order',
        'no_meja',
        'kd_pelanggan',
        'nama_user',
        'tanggal',
        'waktu',
        'keterangan',
        'status_order',
    ];

    protected $casts = [
        'no_meja' => 'integer',
        'tanggal' => 'date',
        'waktu'   => 'datetime',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'no_meja', 'no_meja');
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'order_kd', 'kd_order');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'order_kd', 'kd_order');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }

    public function updateStatus(string $status): void
    {
        $this->status_order = $status;
        $this->save();
    }
}
