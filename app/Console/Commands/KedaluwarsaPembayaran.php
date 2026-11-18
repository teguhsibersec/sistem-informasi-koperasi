<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Console\Command;

class KedaluwarsaPembayaran extends Command
{
    protected $signature = 'pembayaran:kedaluwarsa';
    protected $description = 'Batalkan pesanan QRIS yang lewat batas waktu dan kembalikan stoknya';

    public function handle(PembayaranService $layanan): int
    {
        $ids = Pembayaran::where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'menunggu')
            ->where('kedaluwarsa_pada', '<=', now())
            ->pluck('id_pembayaran');

        $jumlah = 0;
        foreach ($ids as $id) {
            try {
                $jumlah += $layanan->kedaluwarsakan($id) ? 1 : 0;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->info("{$jumlah} pembayaran kedaluwarsa diproses.");

        return self::SUCCESS;
    }
}
