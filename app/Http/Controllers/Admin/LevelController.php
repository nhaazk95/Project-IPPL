<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::withCount('users')->get();
        return view('admin.level.index', compact('levels'));
    }

    public function update(Request $request, string $id)
    {
        $level = Level::findOrFail($id);
        $request->validate(['nama_level' => 'required|string|max:50']);
        $level->update(['nama_level' => $request->nama_level]);
        return back()->with('success', 'Level "' . $request->nama_level . '" berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $level = Level::withCount('users')->findOrFail($id);
        if ($level->users_count > 0) {
            return back()->with('error', 'Level tidak bisa dihapus karena masih ada ' . $level->users_count . ' user.');
        }
        $level->delete();
        return back()->with('success', 'Level berhasil dihapus.');
    }
}
