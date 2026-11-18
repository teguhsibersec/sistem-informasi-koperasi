<?php

namespace App\Support;

use Carbon\CarbonInterface;

class Tanggal
{
    private const PENDEK = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    private const PENUH = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    /** 21 Okt 2026 */
    public static function pendek(CarbonInterface $t): string
    {
        return $t->format('j') . ' ' . self::PENDEK[$t->month - 1] . ' ' . $t->year;
    }

    /** Oktober 2026 */
    public static function bulan(CarbonInterface $t): string
    {
        return self::PENUH[$t->month - 1] . ' ' . $t->year;
    }
}
