<?php

namespace App\Services\Qris;

use App\Exceptions\QrisBelumDiaturException;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Penjualan;
use DomainException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/** Midtrans (sandbox secara default). Kunci dari .env: MIDTRANS_SERVER_KEY, MIDTRANS_BASE_URL. */
class GatewayQrisProvider implements QrisProvider
{
    public function nama(): string
    {
        return 'gateway';
    }

    public function buat(Penjualan $penjualan, Pembayaran $pembayaran): array
    {
        $kunci = config('qris.midtrans.server_key');
        if (config('qris.midtrans.is_production')
            || ! is_string($kunci)
            || ! str_starts_with($kunci, 'SB-Mid-server-')) {
            throw new QrisBelumDiaturException('Lengkapi MIDTRANS_SERVER_KEY Sandbox dan pastikan MIDTRANS_IS_PRODUCTION=false.');
        }

        $orderId = $penjualan->no_nota_penjualan . '-' . $pembayaran->id_pembayaran;
        $menit = max(1, (int) Pengaturan::ambil('qris_masa_berlaku_menit', config('qris.masa_berlaku_default')));
        $caBundle = config('qris.midtrans.ca_bundle');
        if (! is_string($caBundle) || ! is_file($caBundle) || ! is_readable($caBundle)) {
            throw new QrisBelumDiaturException('CA bundle Midtrans tidak ditemukan atau tidak dapat dibaca. Periksa MIDTRANS_CA_BUNDLE.');
        }

        try {
            $res = Http::withOptions(['verify' => $caBundle])->withBasicAuth($kunci, '')->acceptJson()->timeout(15)
                ->post(rtrim(config('qris.midtrans.base_url'), '/') . '/v2/charge', [
                    'payment_type' => 'qris',
                    'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $pembayaran->jumlah_tagihan],
                    'qris' => ['acquirer' => 'gopay'],
                    'custom_expiry' => ['expiry_duration' => $menit, 'unit' => 'minute'],
                ]);
        } catch (ConnectionException $e) {
            throw new DomainException('Tidak dapat terhubung ke Midtrans Sandbox. Coba beberapa saat lagi.', previous: $e);
        }

        if (! $res->successful() || ! $res->json('qr_string')) {
            throw new DomainException('Gagal membuat QRIS di gateway: ' . ($res->json('status_message') ?? 'HTTP ' . $res->status()));
        }

        return ['kode_referensi' => $orderId, 'qris_payload' => $res->json('qr_string')];
    }

    /** signature_key = SHA512(order_id + status_code + gross_amount + server_key) */
    public function verifikasiSignature(array $data): bool
    {
        $kunci = config('qris.midtrans.server_key');
        if (config('qris.midtrans.is_production')
            || ! is_string($kunci)
            || ! str_starts_with($kunci, 'SB-Mid-server-')) {
            return false;
        }

        $sig = hash('sha512', ($data['order_id'] ?? '') . ($data['status_code'] ?? '') . ($data['gross_amount'] ?? '') . $kunci);

        return hash_equals($sig, (string) ($data['signature_key'] ?? ''));
    }
}
