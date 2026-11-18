<?php

namespace App\Support;

class FormatLaporan
{
    /** Format satu sel laporan menurut tipe kolom: teks | angka | rupiah | persen. */
    public static function sel(mixed $nilai, string $tipe): string
    {
        if ($nilai === null || $nilai === '') {
            return '-';
        }

        return match ($tipe) {
            'angka' => number_format((float) $nilai, 0, ',', '.'),
            'rupiah' => Rupiah::format($nilai),
            'persen' => number_format((float) $nilai, 1, ',', '.') . '%',
            default => (string) $nilai,
        };
    }
}
