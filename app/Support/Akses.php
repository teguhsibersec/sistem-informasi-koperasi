<?php

namespace App\Support;

use App\Models\User;

class Akses
{
    /** Satu sumber kebenaran untuk cek peran: admin, kasir, gudang, anggota. */
    public static function punya(?User $user, string $peran): bool
    {
        if (! $user) {
            return false;
        }

        return match ($peran) {
            'anggota' => $user->isAnggota(),
            'admin' => $user->isAdmin() || $user->pengurus?->jabatan_pengurus === 'ketua',
            default => $user->punyaPeran($peran),
        };
    }

    public static function punyaSalahSatu(?User $user, array $peran): bool
    {
        foreach ($peran as $p) {
            if (self::punya($user, $p)) {
                return true;
            }
        }

        return false;
    }
}
