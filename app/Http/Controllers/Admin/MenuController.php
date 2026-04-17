<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('kategori');

        if ($request->filled('search')) {
            $query->where('name_menu', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $menus    = $query->orderBy('name_menu')->paginate(12);
        $kategoris = Kategori::orderBy('name_kategori')->get();

        return view('admin.menu.index', compact('menus', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_menu'  => 'required|string|max:100',
            'kategori_id'=> 'required|exists:kategoris,kd_kategori',
            'harga'      => 'required|integer|min:0',
            'description'=> 'nullable|string',
            'status'     => 'required|in:tersedia,habis',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name_menu', 'kategori_id', 'harga', 'description', 'status']);
        $data['kd_menu'] = 'MNU-' . strtoupper(Str::random(6));

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }

        Menu::create($data);

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(string $kd_menu)
    {
        $menu     = Menu::findOrFail($kd_menu);
        $kategoris = Kategori::orderBy('name_kategori')->get();
        return response()->json(['menu' => $menu, 'kategoris' => $kategoris]);
    }

    public function update(Request $request, string $kd_menu)
    {
        $menu = Menu::findOrFail($kd_menu);

        $request->validate([
            'name_menu'  => 'required|string|max:100',
            'kategori_id'=> 'required|exists:kategoris,kd_kategori',
            'harga'      => 'required|integer|min:0',
            'description'=> 'nullable|string',
            'status'     => 'required|in:tersedia,habis',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name_menu', 'kategori_id', 'harga', 'description', 'status']);

        if ($request->hasFile('photo')) {
            if ($menu->photo) Storage::disk('public')->delete($menu->photo);
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu->update($data);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(string $kd_menu)
    {
        $menu = Menu::findOrFail($kd_menu);
        if ($menu->photo) Storage::disk('public')->delete($menu->photo);
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }

    public function toggleStatus(string $kd_menu)
    {
        $menu = Menu::findOrFail($kd_menu);
        $menu->status = $menu->status === 'tersedia' ? 'habis' : 'tersedia';
        $menu->save();

        return back()->with('success', 'Status menu diperbarui.');
    }
}