<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailOrderTemporary;
use App\Models\Meja;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index()
    {
        $mejas     = Meja::orderBy('no_meja')->paginate(20);
        $totalMeja = Meja::count();
        $mejaStats = [
            'tersedia' => Meja::where('status', 'tersedia')->count(),
            'terisi'   => Meja::where('status', 'terisi')->count(),
        ];
        return view('admin.meja.index', compact('mejas', 'totalMeja', 'mejaStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_meja' => 'required|integer|min:1|unique:mejas,no_meja',
            'status'  => 'required|in:tersedia,terisi',
        ]);
        Meja::create($request->only(['no_meja', 'status']));
        return back()->with('success', 'Meja ' . $request->no_meja . ' berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $meja = Meja::findOrFail($id);
        $request->validate([
            'no_meja' => 'required|integer|min:1|unique:mejas,no_meja,' . $id,
            'status'  => 'required|in:tersedia,terisi',
        ]);
        $meja->update($request->only(['no_meja', 'status']));
        return back()->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $meja = Meja::findOrFail($id);
        // Tidak ada pengecekan status, karena user sekarang hanya bisa hapus via modal edit
        $meja->delete();
        return back()->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Force-logout pelanggan di meja tertentu.
     * Kosongkan keranjang temp + bebaskan meja.
     */
    public function kosongkanMeja(string $id)
    {
        $meja = Meja::findOrFail($id);

        // Cari pelanggan aktif di meja ini (yang paling baru login)
        $pelanggan = Pelanggan::where('no_meja', $meja->no_meja)
            ->latest('login_at')
            ->first();

        if ($pelanggan) {
            DetailOrderTemporary::where('pelanggan_kd', $pelanggan->kd_pelanggan)->delete();
        }

        // Kosongkan meja
        $meja->update(['status' => 'tersedia']);

        return back()->with('success', 'Meja ' . $meja->no_meja . ' berhasil dikosongkan. Pelanggan akan otomatis keluar.');
    }
}