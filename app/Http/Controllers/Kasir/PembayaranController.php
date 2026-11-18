<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Services\PembayaranService;
use App\Services\PenjualanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function show(Penjualan $penjualan)
    {
        $b = $penjualan->load('pembayaranTerakhir')->pembayaranTerakhir;
        abort_unless($b && $b->metode_pembayaran === 'qris', 404);

        if ($b->status_pembayaran === 'berhasil') {
            return redirect()->route('nota.show', $penjualan);
        }

        return view('kasir.bayar-qris', ['penjualan' => $penjualan, 'pembayaran' => $b, 'sisa' => $this->sisaDetik($b)]);
    }

    /** Dipolling browser tiap beberapa detik. */
    public function status(Penjualan $penjualan): JsonResponse
    {
        $b = $penjualan->load('pembayaranTerakhir')->pembayaranTerakhir;
        abort_unless($b, 404);

        return response()->json([
            'status' => $b->status_pembayaran,
            'penjualan' => $penjualan->status_penjualan,
            'sisa_detik' => $this->sisaDetik($b),
        ]);
    }

    public function lunas(Request $request, Penjualan $penjualan, PembayaranService $layanan)
    {
        $b = $penjualan->load('pembayaranTerakhir')->pembayaranTerakhir;
        abort_unless($b, 404);

        try {
            $layanan->tandaiLunas($b->id_pembayaran, $request->user()->id_pengurus);
        } catch (\DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return redirect()->route('nota.show', [$penjualan, 'cetak' => 1])->with('sukses', 'Pembayaran QRIS lunas.');
    }

    public function batal(Request $request, Penjualan $penjualan, PenjualanService $layanan)
    {
        $layanan->batalkan($penjualan->id_penjualan, $request->user()->id_pengurus, 'Dibatalkan pengurus di layar pembayaran QRIS');

        return redirect()->route('kasir.pos')->with('sukses', 'Transaksi dibatalkan dan stok dikembalikan.');
    }

    private function sisaDetik($pembayaran): int
    {
        return $pembayaran->kedaluwarsa_pada ? max(0, $pembayaran->kedaluwarsa_pada->getTimestamp() - now()->getTimestamp()) : 0;
    }
}
