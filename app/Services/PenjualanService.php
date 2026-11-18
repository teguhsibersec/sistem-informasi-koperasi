<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanService
{
    public function __construct(private StokService $stok)
    {
    }

    /** Batalkan transaksi: stok dikembalikan, pembayaran 'menunggu' ditutup, dicatat di riwayat_stok dan log aktivitas. Idempoten. */
    public function batalkan(int $idPenjualan, ?int $idPengurus, string $alasan, string $statusPembayaran = 'gagal'): Penjualan
    {
        return DB::transaction(function () use ($idPenjualan, $idPengurus, $alasan, $statusPembayaran) {
            $p = Penjualan::lockForUpdate()->findOrFail($idPenjualan);
            if ($p->status_penjualan === 'batal') {
                return $p;
            }

            $sebelum = $p->status_penjualan;
            $items = $p->details()->pluck('qty_detail_penjualan', 'id_barang')->all();
            $this->stok->kembalikan($items, 'batal', $p->no_nota_penjualan, $idPengurus, $alasan);

            Pembayaran::where('id_penjualan', $p->id_penjualan)
                ->where('status_pembayaran', 'menunggu')
                ->update(['status_pembayaran' => $statusPembayaran, 'updated_at' => now()]);

            $p->update(['status_penjualan' => 'batal', 'catatan_penjualan' => Str::limit($alasan, 255, '')]);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aksi' => 'batal',
                'tabel' => 'penjualan',
                'id_rekaman' => (string) $p->id_penjualan,
                'perubahan' => ['alasan' => $alasan, 'status_sebelum' => $sebelum],
            ]);

            return $p;
        });
    }
}
