<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Kategori;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::all();

        $menus = Menu::query();

        if ($request->kategori) {
            $menus->where('kategori_id', $request->kategori); // ✅ FIX
        }

        if ($request->search) {
            $menus->where('name_menu', 'like', '%' . $request->search . '%');
        }

        $menus = $menus->get();

        return view('pelanggan.menu', compact('menus', 'kategoris'));
    }

    public function show($kd_menu)
    {
        $menu = Menu::where('kd_menu', $kd_menu)
            ->where('status', 'tersedia')
            ->firstOrFail();

        $related = Menu::where('kategori_id', $menu->kategori_id) 
            ->where('kd_menu', '!=', $menu->kd_menu)
            ->where('status', 'tersedia')
            ->limit(4)
            ->get();

        return view('pelanggan.menu-detail', compact('menu', 'related'));
    }
}