<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotaService
{
    /** Format CHAR(12): KP + YYMMDD + 4 digit urut harian. Panggil di dalam transaksi. */
    public function berikutnya(Carbon $waktu): string
    {
        $awalan = 'KP' . $waktu->format('ymd');

        $terakhir = DB::table('penjualan')
            ->where('no_nota_penjualan', 'like', $awalan . '%')
            ->lockForUpdate()
            ->max('no_nota_penjualan');

        return $awalan . sprintf('%04d', $terakhir ? ((int) substr($terakhir, 8)) + 1 : 1);
    }
}
