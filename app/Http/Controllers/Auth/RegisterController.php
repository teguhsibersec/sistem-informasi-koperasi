<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /** Pendaftaran mandiri: membuat data anggota baru + akun anggota dalam satu transaksi. */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $terakhir = DB::table('anggota')->lockForUpdate()->orderByDesc('no_anggota')->value('no_anggota');
            $nomor = sprintf('A%05d', $terakhir ? ((int) substr($terakhir, 1)) + 1 : 1);

            $anggota = Anggota::create([
                'no_anggota' => $nomor,
                'nis_anggota' => $request->nis,
                'nama_anggota' => $request->nama,
                'kelas_anggota' => $request->kelas,
                'alamat_anggota' => $request->alamat,
                'kota_anggota' => $request->kota,
                'no_hp_anggota' => $request->no_hp,
                'status_anggota' => 'aktif',
                'tgl_daftar_anggota' => now()->toDateString(),
            ]);

            return User::create([
                'name' => $request->nama,
                'username' => $request->nis,   // login bisa pakai NIS
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'anggota',
                'id_anggota' => $anggota->id_anggota,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('toko.beranda')->with('sukses', 'Pendaftaran berhasil. Selamat datang di '.config('koperasi.nama').'!');
    }
}
