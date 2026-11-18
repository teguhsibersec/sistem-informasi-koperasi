<?php

namespace App\Support;

use App\Models\User;

class HalamanAwal
{
    /** Halaman tujuan setelah login, sesuai peran. */
    public static function untuk(?User $user): string
    {
        if (! $user) {
            return route('login');
        }

        if ($user->isAnggota()) {
            return route('toko.beranda');
        }

        $peran = $user->pengurus?->jabatan_pengurus;

        if ($user->isAdmin() || $peran === 'ketua') {
            return route('admin.dashboard');
        }

        return $peran === 'sekretaris' ? route('admin.produk.index') : route('kasir.pos');
    }
}
