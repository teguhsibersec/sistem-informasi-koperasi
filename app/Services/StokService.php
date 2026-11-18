<?php

namespace App\Services;

use App\Exceptions\StokTidakCukupException;
use App\Models\Barang;
use App\Models\RiwayatStok;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StokService
{
    /**
     * Kurangi stok beberapa barang sekaligus dengan row locking (harus di dalam DB::transaction).
     * Barang dikunci berurutan menurut id supaya tidak terjadi deadlock antar transaksi.
     *
     * @param  array<int,int>  $items  [id_barang => qty]
     * @return Collection<int,Barang>  barang terkunci (berisi harga jual saat ini)
     */
    public function kurangi(array $items, string $tipe, ?string $referensi, ?int $idPengurus, ?string $catatan = null): Collection
    {
        $this->pastikanDalamTransaksi();
        ksort($items);

        $barang = Barang::whereIn('id_barang', array_keys($items))->orderBy('id_barang')->lockForUpdate()->get()->keyBy('id_barang');

        foreach ($items as $id => $qty) {
            $b = $barang->get($id);
            if (! $b || $b->status_barang !== 'aktif') {
                throw new DomainException('Barang tidak tersedia atau sudah dinonaktifkan.');
            }
            if ($b->stok_barang < $qty) {
                throw new StokTidakCukupException($b->nama_barang, $b->stok_barang, $qty);
            }
            $this->catat($b, -$qty, $tipe, $referensi, $idPengurus, $catatan);
        }

        return $barang;
    }

    /** Kembalikan stok (pembatalan / kedaluwarsa). Barang yang sudah di-soft-delete tetap dipulihkan. */
    public function kembalikan(array $items, string $tipe, ?string $referensi, ?int $idPengurus, ?string $catatan = null): void
    {
        $this->pastikanDalamTransaksi();
        ksort($items);

        $barang = Barang::withTrashed()->whereIn('id_barang', array_keys($items))->orderBy('id_barang')->lockForUpdate()->get()->keyBy('id_barang');

        foreach ($items as $id => $qty) {
            if ($b = $barang->get($id)) {
                $this->catat($b, $qty, $tipe, $referensi, $idPengurus, $catatan);
            }
        }
    }

    /**
     * Penyesuaian manual (stok opname): setel stok ke jumlah sebenarnya, wajib beralasan.
     * Tidak ada perubahan -> tidak ada catatan riwayat.
     */
    public function sesuaikan(int $idBarang, int $stokBaru, string $alasan, ?int $idPengurus): Barang
    {
        $this->pastikanDalamTransaksi();

        if ($stokBaru < 0) {
            throw new DomainException('Stok tidak boleh negatif.');
        }

        $b = Barang::withTrashed()->lockForUpdate()->findOrFail($idBarang);
        $delta = $stokBaru - $b->stok_barang;

        if ($delta !== 0) {
            $this->catat($b, $delta, 'penyesuaian', null, $idPengurus, $alasan);
        }

        return $b;
    }

    private function catat(Barang $b, int $delta, string $tipe, ?string $referensi, ?int $idPengurus, ?string $catatan): void
    {
        $sebelum = $b->stok_barang;
        $b->stok_barang = $sebelum + $delta;
        $b->save();

        RiwayatStok::create([
            'id_barang' => $b->id_barang,
            'tipe' => $tipe,
            'qty_perubahan' => $delta,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $b->stok_barang,
            'referensi' => $referensi,
            'catatan' => $catatan,
            'id_pengurus' => $idPengurus,
        ]);
    }

    private function pastikanDalamTransaksi(): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('StokService harus dipanggil di dalam DB::transaction.');
        }
    }
}
