<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'nis' => ['required', 'digits_between:4,20', 'unique:anggota,nis_anggota', 'unique:users,username'],
            'kelas' => ['required', 'string', 'max:15'],
            'alamat' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:35'],
            'no_hp' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.digits_between' => 'NIS harus 4-20 digit angka.',
            'nis.unique' => 'NIS sudah terdaftar. Hubungi pengurus koperasi jika akun belum Anda miliki.',
            'kelas.required' => 'Kelas wajib diisi.',
            'no_hp.regex' => 'Telepon harus 8-15 digit angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
