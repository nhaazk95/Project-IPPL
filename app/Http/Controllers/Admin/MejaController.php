<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
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
        if ($meja->status === 'terisi') {
            return back()->with('error', 'Meja sedang terisi, tidak bisa dihapus.');
        }
        $meja->delete();
        return back()->with('success', 'Meja berhasil dihapus.');
    }
}
