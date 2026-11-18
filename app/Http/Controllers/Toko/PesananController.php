<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotaController;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $pesanan = Penjualan::with(['details', 'pembayaranTerakhir'])
            ->where('id_anggota', $request->user()->id_anggota)
            ->orderByDesc('tgl_penjualan')->orderByDesc('id_penjualan')
            ->paginate(10);

        return view('toko.pesanan.index', ['pesanan' => $pesanan]);
    }

    public function show(Request $request, Penjualan $penjualan)
    {
        $this->milikSendiri($request, $penjualan);
        $penjualan->load(['details.barang', 'pembayaranTerakhir']);

        return view('toko.pesanan.show', [
            'penjualan' => $penjualan,
            'bayar' => $penjualan->pembayaranTerakhir,
            'bolehBayar' => $this->bolehBayarQris($penjualan),
            'bolehBatal' => $this->bolehBatal($penjualan),
        ]);
    }

    public function bayar(Request $request, Penjualan $penjualan)
    {
        $this->milikSendiri($request, $penjualan);
        $penjualan->load('pembayaranTerakhir');

        if (! $this->bolehBayarQris($penjualan)) {
            return redirect()->route('toko.pesanan.show', $penjualan)->with('galat', 'Pesanan ini tidak sedang menunggu pembayaran QRIS.');
        }

        return view('toko.pesanan.bayar', [
            'penjualan' => $penjualan,
            'bayar' => $penjualan->pembayaranTerakhir,
            'sisa' => $this->sisaDetik($penjualan->pembayaranTerakhir),
        ]);
    }

    /** Dipolling halaman QRIS. */
    public function status(Request $request, Penjualan $penjualan): JsonResponse
    {
        $this->milikSendiri($request, $penjualan);
        $b = $penjualan->load('pembayaranTerakhir')->pembayaranTerakhir;
        abort_unless($b, 404);

        return response()->json([
            'status' => $b->status_pembayaran,
            'penjualan' => $penjualan->status_penjualan,
            'sisa_detik' => $this->sisaDetik($b),
        ]);
    }

    /** Opsional: unggah bukti transfer/QRIS untuk membantu pengurus memverifikasi (provider manual). */
    public function bukti(Request $request, Penjualan $penjualan)
    {
        $this->milikSendiri($request, $penjualan);
        $penjualan->load('pembayaranTerakhir');

        if (! $this->bolehBayarQris($penjualan)) {
            return back()->with('galat', 'Bukti pembayaran hanya bisa diunggah selama QRIS masih berlaku.');
        }

        $request->validate(
            ['bukti' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
            ['bukti.required' => 'Pilih file bukti pembayaran.', 'bukti.image' => 'Bukti harus berupa gambar.', 'bukti.mimes' => 'Format harus JPG, PNG, atau WEBP.', 'bukti.max' => 'Ukuran gambar maksimal 2 MB.']
        );

        $b = $penjualan->pembayaranTerakhir;
        if ($b->bukti_bayar) {
            Storage::disk('public')->delete($b->bukti_bayar);
        }
        $b->update(['bukti_bayar' => $request->file('bukti')->store('bukti-bayar', 'public')]);

        return back()->with('sukses', 'Bukti pembayaran terkirim. Pengurus akan memverifikasi pembayaran Anda.');
    }

    public function batal(Request $request, Penjualan $penjualan, PenjualanService $layanan)
    {
        $this->milikSendiri($request, $penjualan);
        $penjualan->load('pembayaranTerakhir');

        if (! $this->bolehBatal($penjualan)) {
            return back()->with('galat', 'Pesanan ini tidak dapat dibatalkan. Hubungi pengurus koperasi.');
        }

        $layanan->batalkan($penjualan->id_penjualan, null, 'Dibatalkan oleh anggota');

        return redirect()->route('toko.pesanan.show', $penjualan)->with('sukses', 'Pesanan dibatalkan.');
    }

    public function nota(Request $request, Penjualan $penjualan)
    {
        $this->milikSendiri($request, $penjualan);

        return app(NotaController::class)->show($penjualan);
    }

    public function notaPdf(Request $request, Penjualan $penjualan)
    {
        $this->milikSendiri($request, $penjualan);

        return app(NotaController::class)->pdf($penjualan);
    }

    /** Pesanan milik orang lain disamarkan sebagai 404. */
    private function milikSendiri(Request $request, Penjualan $penjualan): void
    {
        abort_unless($penjualan->id_anggota !== null && $penjualan->id_anggota === $request->user()->id_anggota, 404);
    }

    private function bolehBayarQris(Penjualan $p): bool
    {
        $b = $p->pembayaranTerakhir;

        return $b && $b->metode_pembayaran === 'qris' && $b->status_pembayaran === 'menunggu' && $p->status_penjualan === 'menunggu_bayar';
    }

    /** Boleh batal: belum dibayar (QRIS menunggu, atau cash yang belum dibayar di koperasi). */
    private function bolehBatal(Penjualan $p): bool
    {
        $b = $p->pembayaranTerakhir;

        return $b && $b->status_pembayaran === 'menunggu' && in_array($p->status_penjualan, ['menunggu_bayar', 'diproses'], true);
    }

    private function sisaDetik(?Pembayaran $b): int
    {
        return $b?->kedaluwarsa_pada ? max(0, $b->kedaluwarsa_pada->getTimestamp() - now()->getTimestamp()) : 0;
    }
}
