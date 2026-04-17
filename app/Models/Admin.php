<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Admin extends User
{
    /**
     * Scope default untuk Admin (level_id = 1)
     */
    protected static function booted(): void
    {
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->where('level_id', 1);
        });
    }

    // ==================== METHODS ====================

    /**
     * Kelola level user (tambah/ubah/hapus level)
     */
    public function kelolaLevelUser(array $data, ?int $id = null): void
    {
        if ($id) {
            Level::findOrFail($id)->update($data);
        } else {
            Level::create($data);
        }
    }

    /**
     * Kelola menu (tambah/ubah/hapus menu)
     */
    public function kelolaMenu(array $data, ?string $kdMenu = null): void
    {
        if ($kdMenu) {
            Menu::findOrFail($kdMenu)->update($data);
        } else {
            Menu::create($data);
        }
    }

    /**
     * Kelola kategori (tambah/ubah/hapus kategori)
     */
    public function kelolaKategori(array $data, ?string $kdKategori = null): void
    {
        if ($kdKategori) {
            Kategori::findOrFail($kdKategori)->update($data);
        } else {
            Kategori::create($data);
        }
    }

    /**
     * Kelola meja (tambah/ubah/hapus meja)
     */
    public function kelolaMeja(array $data, ?int $id = null): void
    {
        if ($id) {
            Meja::findOrFail($id)->update($data);
        } else {
            Meja::create($data);
        }
    }

    /**
     * Kelola transaksi (lihat/filter transaksi)
     */
    public function kelolaTransaksi(): \Illuminate\Database\Eloquent\Collection
    {
        return Transaksi::with(['order', 'detailOrders'])->get();
    }

    /**
     * Kelola laporan (generate laporan)
     */
    public function kelolaLaporan(string $startDate, string $endDate): array
    {
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->with(['detailOrders.menu'])
            ->get();

        return [
            'total_transaksi' => $transaksis->count(),
            'total_pendapatan' => $transaksis->sum('total_harga'),
            'data'            => $transaksis,
        ];
    }
}
