<?php

namespace App\Services\Qris;

use App\Exceptions\QrisBelumDiaturException;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Penjualan;
use InvalidArgumentException;

/** Default: QRIS statis koperasi (tabel pengaturan) diubah jadi dinamis; pelunasan dikonfirmasi pengurus. */
class ManualQrisProvider implements QrisProvider
{
    public function nama(): string
    {
        return 'manual';
    }

    public function buat(Penjualan $penjualan, Pembayaran $pembayaran): array
    {
        $statis = trim((string) Pengaturan::ambil('qris_statis', ''));
        if ($statis === '') {
            throw new QrisBelumDiaturException('String QRIS statis koperasi belum diisi di Pengaturan.');
        }

        try {
            $payload = QrisDinamis::dari($statis, $pembayaran->jumlah_tagihan);
        } catch (InvalidArgumentException $e) {
            throw new QrisBelumDiaturException('String QRIS statis koperasi tidak valid. Periksa kembali QRIS di menu Pengaturan.', previous: $e);
        }

        return [
            'kode_referensi' => 'MAN-' . $penjualan->no_nota_penjualan . '-' . $pembayaran->id_pembayaran,
            'qris_payload' => $payload,
        ];
    }
}
