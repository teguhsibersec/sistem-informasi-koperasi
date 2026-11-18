<?php

namespace App\Services\Qris;

use App\Models\Pembayaran;
use App\Models\Penjualan;

interface QrisProvider
{
    /** @return array{kode_referensi:string, qris_payload:string} */
    public function buat(Penjualan $penjualan, Pembayaran $pembayaran): array;

    public function nama(): string;
}
