<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->kd_user . ',kd_user',
            'username' => 'required|string|max:50|unique:users,username,' . $user->kd_user . ',kd_user',
            'no_hp'    => 'nullable|string|max:20',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'photo.image'        => 'File harus berupa gambar.',
            'photo.max'          => 'Ukuran foto maksimal 2MB.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 6 karakter.',
            'email.unique'       => 'Email sudah digunakan akun lain.',
            'username.unique'    => 'Username sudah digunakan akun lain.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username,
            'no_hp'    => $request->no_hp,
        ];

        // Handle foto upload
        if ($request->hasFile('photo')) {
            try {
                if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
                    \Storage::disk('public')->delete($user->foto);
                }
                $path = $request->file('photo')->store('photos', 'public');
                $data['foto'] = $path;
            } catch (\Exception $e) {}
        }

        // Ganti password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->refresh();

        return response()->json([
            'success'   => true,
            'message'   => 'Profil berhasil diperbarui.',
            'name'      => $user->name,
            'photo_url' => $user->foto ? asset('storage/' . $user->foto) : null,
        ]);
    }
}