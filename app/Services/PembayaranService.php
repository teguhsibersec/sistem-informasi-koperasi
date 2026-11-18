<?php

namespace App\Services;

use App\Exceptions\UangKurangException;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use DomainException;
use Illuminate\Support\Facades\DB;

class PembayaranService
{
    public function __construct(private PenjualanService $penjualan)
    {
    }

    /** Tandai QRIS lunas (konfirmasi pengurus atau webhook). Idempoten: panggilan ulang tidak mengubah apa pun. */
    public function tandaiLunas(int $idPembayaran, ?int $idPengurus, ?string $referensi = null): Pembayaran
    {
        return DB::transaction(function () use ($idPembayaran, $idPengurus, $referensi) {
            $b = Pembayaran::lockForUpdate()->findOrFail($idPembayaran);

            if ($b->status_pembayaran === 'berhasil') {
                return $b;
            }
            if ($b->metode_pembayaran !== 'qris') {
                throw new DomainException('Pembayaran cash dikonfirmasi lewat input uang diterima.');
            }
            if ($b->status_pembayaran !== 'menunggu') {
                throw new DomainException("Pembayaran sudah {$b->status_pembayaran}; tidak bisa ditandai lunas.");
            }

            $p = Penjualan::lockForUpdate()->findOrFail($b->id_penjualan);
            if ($p->status_penjualan === 'batal') {
                throw new DomainException('Pesanan sudah dibatalkan.');
            }

            $b->update([
                'status_pembayaran' => 'berhasil',
                'dibayar_pada' => now(),
                'dikonfirmasi_oleh' => $idPengurus,
                'kode_referensi' => $referensi ?? $b->kode_referensi,
            ]);

            $p->update([
                'bayar_penjualan' => $b->jumlah_tagihan,
                'status_penjualan' => $p->sumber_penjualan === 'kasir' ? 'selesai' : 'diproses',
                'id_pengurus' => $p->id_pengurus ?? $idPengurus,
            ]);

            return $b;
        });
    }

    /** Pembayaran cash pesanan online, dikonfirmasi saat barang diambil. */
    public function konfirmasiCash(int $idPembayaran, float $uangDiterima, ?int $idPengurus): Pembayaran
    {
        return DB::transaction(function () use ($idPembayaran, $uangDiterima, $idPengurus) {
            $b = Pembayaran::lockForUpdate()->findOrFail($idPembayaran);

            if ($b->status_pembayaran === 'berhasil') {
                return $b;
            }
            if ($b->metode_pembayaran !== 'cash' || $b->status_pembayaran !== 'menunggu') {
                throw new DomainException('Pembayaran ini tidak bisa dikonfirmasi sebagai cash.');
            }
            if ($uangDiterima < (float) $b->jumlah_tagihan) {
                throw new UangKurangException('Uang diterima kurang dari total tagihan.');
            }

            $p = Penjualan::lockForUpdate()->findOrFail($b->id_penjualan);
            if ($p->status_penjualan === 'batal') {
                throw new DomainException('Pesanan sudah dibatalkan.');
            }

            $b->update([
                'status_pembayaran' => 'berhasil',
                'uang_diterima' => $uangDiterima,
                'kembalian' => $uangDiterima - (float) $b->jumlah_tagihan,
                'dibayar_pada' => now(),
                'dikonfirmasi_oleh' => $idPengurus,
            ]);

            $p->update([
                'bayar_penjualan' => $uangDiterima,
                'status_penjualan' => 'selesai',
                'id_pengurus' => $p->id_pengurus ?? $idPengurus,
            ]);

            return $b;
        });
    }

    /**
     * QRIS lewat batas waktu: pembayaran 'kedaluwarsa', pesanan 'batal', stok kembali.
     * $paksa = true dipakai webhook gateway (abaikan jam lokal). Return true bila ada yang diproses.
     */
    public function kedaluwarsakan(int $idPembayaran, bool $paksa = false): bool
    {
        return DB::transaction(function () use ($idPembayaran, $paksa) {
            $b = Pembayaran::lockForUpdate()->findOrFail($idPembayaran);

            if ($b->metode_pembayaran !== 'qris' || $b->status_pembayaran !== 'menunggu') {
                return false;
            }
            if (! $paksa && $b->kedaluwarsa_pada && $b->kedaluwarsa_pada->isFuture()) {
                return false;
            }

            $this->penjualan->batalkan($b->id_penjualan, null, 'Pembayaran QRIS kedaluwarsa', 'kedaluwarsa');

            return true;
        });
    }

    /** Pembayaran ditolak/dibatalkan gateway. */
    public function gagalkan(int $idPembayaran, string $alasan): bool
    {
        return DB::transaction(function () use ($idPembayaran, $alasan) {
            $b = Pembayaran::lockForUpdate()->findOrFail($idPembayaran);
            if ($b->status_pembayaran !== 'menunggu') {
                return false;
            }
            $this->penjualan->batalkan($b->id_penjualan, null, $alasan, 'gagal');

            return true;
        });
    }
}
