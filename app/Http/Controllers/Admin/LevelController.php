<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $levels = Level::withCount('users')->get();

        $query = User::with('level');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('username', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.level.index', compact('levels', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $level = Level::findOrFail($id);
        $request->validate(['nama_level' => 'required|string|max:50']);
        $level->update(['nama_level' => $request->nama_level]);
        return back()->with('success', 'Level berhasil diperbarui.');
    }
}
