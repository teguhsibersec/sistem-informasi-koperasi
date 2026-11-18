<?php

namespace App\Services;

use App\Exceptions\UangKurangException;
use App\Models\Anggota;
use App\Models\DetailPenjualan;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Penjualan;
use App\Services\Qris\QrisProvider;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Satu pintu pembuatan transaksi (kasir/POS dan checkout online anggota).
 *
 * Aturan status:
 *  - kasir + cash   -> penjualan 'selesai', pembayaran 'berhasil' (kembalian dihitung)
 *  - kasir/online + qris -> penjualan 'menunggu_bayar', pembayaran 'menunggu' + QR + kedaluwarsa
 *  - online + cash  -> penjualan 'diproses', pembayaran 'menunggu' (dikonfirmasi saat barang diambil)
 * Stok dikurangi (di-reserve) saat transaksi dibuat; dikembalikan lewat PenjualanService::batalkan.
 */
class CheckoutService
{
    public function __construct(
        private StokService $stok,
        private NotaService $nota,
        private QrisProvider $qris,
    ) {
    }

    /**
     * @param  array{items:array, metode:string, sumber?:string, id_anggota?:?int, id_pengurus?:?int, uang_diterima?:float|int|null, catatan?:?string}  $data
     */
    public function proses(array $data): Penjualan
    {
        $items = $this->normalisasi($data['items'] ?? []);
        $metode = $data['metode'] ?? '';
        $sumber = $data['sumber'] ?? 'kasir';

        if (! in_array($metode, ['cash', 'qris'], true) || ! in_array($sumber, ['kasir', 'online'], true)) {
            throw new DomainException('Metode atau sumber transaksi tidak valid.');
        }

        return DB::transaction(function () use ($items, $metode, $sumber, $data) {
            $waktu = now();
            $idPengurus = $data['id_pengurus'] ?? null;
            $idAnggota = $data['id_anggota'] ?? null;

            if ($idAnggota && ! Anggota::aktif()->whereKey($idAnggota)->exists()) {
                throw new DomainException('Anggota tidak ditemukan atau nonaktif.');
            }

            $nota = $this->nota->berikutnya($waktu);
            $barang = $this->stok->kurangi($items, 'penjualan', $nota, $idPengurus);

            $total = 0.0;
            foreach ($items as $id => $qty) {
                $total += $qty * (float) $barang[$id]->harga_jual_barang;
            }

            $tunaiKasir = $metode === 'cash' && $sumber === 'kasir';
            $diterima = (float) ($data['uang_diterima'] ?? 0);
            if ($tunaiKasir && $diterima < $total) {
                throw new UangKurangException('Uang diterima kurang dari total belanja.');
            }

            $penjualan = Penjualan::create([
                'no_nota_penjualan' => $nota,
                'tgl_penjualan' => $waktu,
                'id_pengurus' => $sumber === 'kasir' ? $idPengurus : null,
                'id_anggota' => $idAnggota,
                'bayar_penjualan' => $tunaiKasir ? $diterima : 0,
                'sumber_penjualan' => $sumber,
                'status_penjualan' => $tunaiKasir ? 'selesai' : ($metode === 'qris' ? 'menunggu_bayar' : 'diproses'),
                'catatan_penjualan' => $data['catatan'] ?? null,
            ]);

            foreach ($items as $id => $qty) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $id,
                    'qty_detail_penjualan' => $qty,
                    'harga_satuan_detail_penjualan' => $barang[$id]->harga_jual_barang, // snapshot harga
                ]);
            }

            $pembayaran = Pembayaran::create([
                'id_penjualan' => $penjualan->id_penjualan,
                'metode_pembayaran' => $metode,
                'jumlah_tagihan' => $total,
                'status_pembayaran' => $tunaiKasir ? 'berhasil' : 'menunggu',
                'uang_diterima' => $tunaiKasir ? $diterima : null,
                'kembalian' => $tunaiKasir ? $diterima - $total : null,
                'dibayar_pada' => $tunaiKasir ? $waktu : null,
                'dikonfirmasi_oleh' => $tunaiKasir ? $idPengurus : null,
                'kedaluwarsa_pada' => $metode === 'qris'
                    ? $waktu->copy()->addMinutes(max(1, (int) Pengaturan::ambil('qris_masa_berlaku_menit', config('qris.masa_berlaku_default'))))
                    : null,
            ]);

            if ($metode === 'qris') {
                $pembayaran->update($this->qris->buat($penjualan, $pembayaran)); // gagal -> seluruh transaksi di-rollback
            }

            return $penjualan->load(['details.barang', 'pembayaranTerakhir', 'anggota']);
        }, 3);
    }

    /** Terima [['id_barang'=>1,'qty'=>2], ...] atau [1 => 2]; gabungkan duplikat. */
    private function normalisasi(array $items): array
    {
        $hasil = [];
        foreach ($items as $k => $v) {
            [$id, $qty] = is_array($v) ? [$v['id_barang'] ?? null, $v['qty'] ?? null] : [$k, $v];
            if (! is_numeric($id) || ! is_numeric($qty) || (int) $qty < 1) {
                throw new DomainException('Jumlah barang tidak valid.');
            }
            $hasil[(int) $id] = ($hasil[(int) $id] ?? 0) + (int) $qty;
        }
        if (! $hasil) {
            throw new DomainException('Keranjang kosong.');
        }
        ksort($hasil);

        return $hasil;
    }
}
