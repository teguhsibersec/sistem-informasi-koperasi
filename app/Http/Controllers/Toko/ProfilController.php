<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function show(Request $request)
    {
        return view('toko.profil', ['user' => $request->user(), 'anggota' => $request->user()->anggota]);
    }

    /** Nama, NIS, kelas hanya bisa diubah pengurus. Anggota mengubah alamat, kota, telepon, email, dan password. */
    public function update(Request $request)
    {
        $user = $request->user();

        $d = $request->validate([
            'no_hp' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
            'alamat' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:35'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password_lama' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'no_hp.regex' => 'Telepon harus 8-15 digit angka.',
            'email.unique' => 'Email sudah dipakai.',
            'password_lama.required_with' => 'Masukkan password lama untuk mengganti password.',
            'password_lama.current_password' => 'Password lama salah.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($user, $d) {
            $user->anggota->update(['no_hp_anggota' => $d['no_hp'] ?? null, 'alamat_anggota' => $d['alamat'] ?? null, 'kota_anggota' => $d['kota'] ?? null]);
            $user->email = $d['email'];
            if (! empty($d['password'])) {
                $user->password = $d['password'];
            }
            $user->save();
        });

        return back()->with('sukses', 'Profil diperbarui.');
    }
}
