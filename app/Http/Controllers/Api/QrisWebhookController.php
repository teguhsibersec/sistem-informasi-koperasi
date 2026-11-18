<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use App\Services\Qris\GatewayQrisProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Webhook notifikasi gateway (Midtrans). Diverifikasi signature, idempoten (notifikasi ganda aman). */
class QrisWebhookController extends Controller
{
    public function __invoke(Request $request, GatewayQrisProvider $gateway, PembayaranService $layanan): JsonResponse
    {
        $data = $request->all();

        if (! $gateway->verifikasiSignature($data)) {
            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $pembayaran = Pembayaran::where('kode_referensi', $data['order_id'] ?? '')->first();
        if (! $pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan.'], 404);
        }
        if ((int) ($data['gross_amount'] ?? 0) !== (int) $pembayaran->jumlah_tagihan) {
            return response()->json(['message' => 'Nominal tidak cocok.'], 400);
        }

        $status = $data['transaction_status'] ?? '';
        try {
            if (in_array($status, ['settlement', 'capture'], true)) {
                $layanan->tandaiLunas($pembayaran->id_pembayaran, null);
            } elseif ($status === 'expire') {
                $layanan->kedaluwarsakan($pembayaran->id_pembayaran, true);
            } elseif (in_array($status, ['cancel', 'deny', 'failure'], true)) {
                $layanan->gagalkan($pembayaran->id_pembayaran, "Gateway: {$status}");
            }
        } catch (\DomainException $e) {
            // mis. pembayaran masuk setelah pesanan batal: perlu refund manual
            Log::warning('Webhook QRIS tidak dapat diterapkan: ' . $e->getMessage(), ['order_id' => $data['order_id'] ?? null]);
        }

        return response()->json(['message' => 'OK']);
    }
}
