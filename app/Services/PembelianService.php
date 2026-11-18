<?php

namespace App\Services;

use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Support\Aktivitas;
use DomainException;
use Illuminate\Support\Facades\DB;

class PembelianService
{
    public function __construct(private StokService $stok)
    {
    }

    /** @param array<int, array{id_barang:int, qty:int, harga:float|int|string}> $items */
    public function buat(int $idPemasok, string $noFaktur, string $tanggal, array $items): Pembelian
    {
        if (! $items) {
            throw new DomainException('Pembelian harus memiliki minimal satu barang.');
        }

        return DB::transaction(function () use ($idPemasok, $noFaktur, $tanggal, $items) {
            $p = Pembelian::create([
                'no_faktur_pembelian' => $noFaktur,
                'tgl_pembelian' => $tanggal,
                'id_pemasok' => $idPemasok,
                'status_pembelian' => 'dipesan',
            ]);

            foreach ($items as $i) {
                DetailPembelian::create([
                    'id_pembelian' => $p->id_pembelian,
                    'id_barang' => $i['id_barang'],
                    'qty_detail_pembelian' => $i['qty'],
                    'harga_beli_detail_pembelian' => $i['harga'],
                ]);
            }

            Aktivitas::catat('tambah', 'pembelian', $p->id_pembelian, ['faktur' => $noFaktur, 'jumlah_item' => count($items)]);

            return $p;
        });
    }

    /** Barang diterima: stok bertambah dan tercatat di riwayat_stok. Idempoten (diterima dua kali tidak menambah dua kali). */
    public function terima(int $idPembelian, ?int $idPengurus): Pembelian
    {
        return DB::transaction(function () use ($idPembelian, $idPengurus) {
            $p = Pembelian::lockForUpdate()->findOrFail($idPembelian);
            if ($p->status_pembelian === 'diterima') {
                return $p;
            }

            $items = $p->details()->pluck('qty_detail_pembelian', 'id_barang')->all();
            $this->stok->kembalikan($items, 'pembelian', $p->no_faktur_pembelian, $idPengurus, 'Penerimaan barang');

            $p->update(['status_pembelian' => 'diterima']);
            Aktivitas::catat('ubah', 'pembelian', $p->id_pembelian, ['status' => ['dipesan', 'diterima']]);

            return $p;
        });
    }

    public function hapus(int $idPembelian): void
    {
        DB::transaction(function () use ($idPembelian) {
            $p = Pembelian::lockForUpdate()->findOrFail($idPembelian);
            if ($p->status_pembelian === 'diterima') {
                throw new DomainException('Pembelian yang sudah diterima tidak dapat dihapus.');
            }

            DetailPembelian::where('id_pembelian', $idPembelian)->delete();
            $p->delete();
            Aktivitas::catat('hapus', 'pembelian', $idPembelian, ['faktur' => $p->no_faktur_pembelian]);
        });
    }
}
