<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutKasirRequest;
use App\Models\Anggota;
use App\Models\Barang;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('kasir.pos');
    }

    public function cariBarang(Request $request): JsonResponse
    {
        $q = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        $barang = Barang::aktif()
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('kode_barang', 'like', "%{$q}%")->orWhere('nama_barang', 'like', "%{$q}%")))
            ->orderBy('nama_barang')
            ->limit(24)
            ->get()
            ->map(fn (Barang $b) => [
                'id_barang' => $b->id_barang,
                'kode_barang' => $b->kode_barang,
                'nama_barang' => $b->nama_barang,
                'harga' => (float) $b->harga_jual_barang,
                'stok' => $b->stok_barang,
            ]);

        return response()->json($barang);
    }

    public function cariAnggota(Request $request): JsonResponse
    {
        $q = addcslashes(trim((string) $request->query('q', '')), '%_\\');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $anggota = Anggota::aktif()
            ->where(fn ($w) => $w->where('nis_anggota', 'like', "{$q}%")->orWhere('no_anggota', 'like', "{$q}%")->orWhere('nama_anggota', 'like', "%{$q}%"))
            ->orderBy('nama_anggota')
            ->limit(8)
            ->get(['id_anggota', 'no_anggota', 'nis_anggota', 'nama_anggota']);

        return response()->json($anggota);
    }

    public function checkout(CheckoutKasirRequest $request, CheckoutService $checkout): JsonResponse
    {
        try {
            $penjualan = $checkout->proses([
                'items' => $request->input('items'),
                'metode' => $request->input('metode'),
                'sumber' => 'kasir',
                'id_anggota' => $request->input('id_anggota'),
                'id_pengurus' => $request->user()->id_pengurus,
                'uang_diterima' => $request->input('uang_diterima'),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'no_nota' => $penjualan->no_nota_penjualan,
            'redirect' => $request->input('metode') === 'qris'
                ? route('kasir.bayar', $penjualan)
                : route('nota.show', [$penjualan, 'cetak' => 1]),
        ]);
    }
}
