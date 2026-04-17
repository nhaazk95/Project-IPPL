<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('level');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
        }
        $users  = $query->orderBy('name')->paginate(15);
        $levels = Level::all();
        return view('admin.user.user', compact('users', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'level_id' => 'required|exists:levels,id',
        ]);

        User::create([
            'kd_user'  => 'USR-' . strtoupper(Str::random(6)),
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(string $kd_user)
    {
        $user   = User::findOrFail($kd_user);
        $levels = Level::all();
        return response()->json(['user' => $user, 'levels' => $levels]);
    }

    public function update(Request $request, string $kd_user)
    {
        $user = User::findOrFail($kd_user);

        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $kd_user . ',kd_user',
            'email'    => 'required|email|unique:users,email,' . $kd_user . ',kd_user',
            'level_id' => 'required|exists:levels,id',
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->only(['name', 'username', 'email', 'level_id']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(string $kd_user)
    {
        $user = User::findOrFail($kd_user);
        if ($user->kd_user === auth()->user()->kd_user) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}