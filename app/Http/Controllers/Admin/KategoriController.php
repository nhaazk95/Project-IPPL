<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('menus')->orderBy('name_kategori')->paginate(10);
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_kategori' => 'required|string|max:100',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name_kategori', 'description']);
        $data['kd_kategori'] = 'KAT-' . strtoupper(Str::random(5));

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('kategoris', 'public');
        }

        Kategori::create($data);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, string $kd_kategori)
    {
        $kategori = Kategori::findOrFail($kd_kategori);

        $request->validate([
            'name_kategori' => 'required|string|max:100',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name_kategori', 'description']);

        if ($request->hasFile('photo')) {
            if ($kategori->photo) Storage::disk('public')->delete($kategori->photo);
            $data['photo'] = $request->file('photo')->store('kategoris', 'public');
        }

        $kategori->update($data);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(string $kd_kategori)
    {
        $kategori = Kategori::findOrFail($kd_kategori);
        if ($kategori->menus()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki menu.');
        }
        if ($kategori->photo) Storage::disk('public')->delete($kategori->photo);
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}