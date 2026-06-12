<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        // Load levels beserta users-nya, urutkan berdasarkan nama level
        $levels = Level::with(['users' => function ($q) {
            $q->select('kd_user', 'name', 'level_id')->orderBy('name');
        }])->withCount('users')->orderBy('nama_level')->get();

        return view('admin.level.index', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_level' => 'required|string|max:50|unique:levels,nama_level',
        ], [
            'nama_level.unique' => 'Level dengan nama ini sudah ada.',
        ]);

        Level::create(['nama_level' => $request->nama_level]);

        return back()->with('success', 'Level "' . $request->nama_level . '" berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $level = Level::findOrFail($id);
        $request->validate([
            'nama_level' => 'required|string|max:50|unique:levels,nama_level,' . $id,
        ], [
            'nama_level.unique' => 'Nama level sudah digunakan level lain.',
        ]);

        $level->update(['nama_level' => $request->nama_level]);

        return back()->with('success', 'Level berhasil diperbarui menjadi "' . $request->nama_level . '".');
    }

    public function destroy(string $id)
    {
        $level = Level::withCount('users')->findOrFail($id);

        if ($level->users_count > 0) {
            return back()->with('error',
                'Level tidak bisa dihapus — masih ada ' . $level->users_count . ' user dengan level ini.'
            );
        }

        $nama = $level->nama_level;
        $level->delete();

        return back()->with('success', 'Level "' . $nama . '" berhasil dihapus.');
    }

    public function destroyUser(string $kd_user)
    {
        $user = User::findOrFail($kd_user);

        if (auth()->user()->kd_user === $kd_user) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang login.');
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('success', 'Akun pegawai "' . $nama . '" (' . $kd_user . ') berhasil dihapus.');
    }
}