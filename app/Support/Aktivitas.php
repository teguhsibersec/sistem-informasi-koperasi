<?php

namespace App\Support;

use App\Models\LogAktivitas;

class Aktivitas
{
    /** Catat siapa mengubah apa (log aktivitas dasar). */
    public static function catat(string $aksi, string $tabel, int|string|null $id, array $perubahan = []): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'tabel' => $tabel,
            'id_rekaman' => $id !== null ? (string) $id : null,
            'perubahan' => $perubahan ?: null,
        ]);
    }
}
