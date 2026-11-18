<?php

namespace App\Support;

class Rupiah
{
    public static function format(float|int|string|null $angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}
