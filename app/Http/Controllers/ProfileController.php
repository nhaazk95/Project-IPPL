<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:100',
            'username'     => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->kd_user, 'kd_user')],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($user->kd_user, 'kd_user')],
            'no_hp'        => 'nullable|string|max:20',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password'     => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
        ];

        // Upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_profil', 'public');
        }

        // Ganti password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui!']);
    }
}