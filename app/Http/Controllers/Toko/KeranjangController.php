<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\KeranjangService;
use DomainException;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function __construct(private KeranjangService $keranjang)
    {
    }

    private function anggota(Request $request): Anggota
    {
        return $request->user()->anggota ?? abort(403);
    }

    public function index(Request $request)
    {
        $isi = $this->keranjang->isi($this->anggota($request));

        return view('toko.keranjang', [
            'isi' => $isi,
            'total' => $isi->sum('subtotal'),
            'siap' => $this->keranjang->siapCheckout($isi),
        ]);
    }

    public function tambah(Request $request)
    {
        $d = $request->validate([
            'id_barang' => ['required', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $this->keranjang->tambah($this->anggota($request), (int) $d['id_barang'], (int) ($d['qty'] ?? 1));
        } catch (DomainException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'sukses' => false,
                    'pesan' => $e->getMessage(),
                ], 422);
            }

            return back()->with('galat', $e->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            $isi = $this->keranjang->isi($this->anggota($request));
            $items = $isi->map(fn ($x) => [
                'id_barang' => $x['id_barang'],
                'nama' => $x['barang']?->nama_barang ?? 'Produk',
                'harga' => (int) $x['harga'],
                'harga_rp' => \App\Support\Rupiah::format($x['harga']),
                'gambar_url' => $x['barang']?->gambar_url,
                'qty' => $x['qty'],
                'subtotal' => (int) $x['subtotal'],
                'subtotal_rp' => \App\Support\Rupiah::format($x['subtotal']),
            ])->values();

            $total = (int) $isi->sum('subtotal');

            return response()->json([
                'sukses' => true,
                'pesan' => 'Produk berhasil ditambahkan ke keranjang.',
                'jumlah' => (int) $isi->sum('qty'),
                'total' => $total,
                'total_rp' => \App\Support\Rupiah::format($total),
                'items' => $items,
            ]);
        }

        return back()->with('sukses', 'Produk ditambahkan ke keranjang.');
    }

    public function ubah(Request $request, int $idBarang)
    {
        $d = $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:999']]);

        try {
            $this->keranjang->ubah($this->anggota($request), $idBarang, (int) $d['qty']);
        } catch (DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return back()->with('sukses', 'Keranjang diperbarui.');
    }

    public function hapus(Request $request, int $idBarang)
    {
        $this->keranjang->hapus($this->anggota($request), $idBarang);

        return back()->with('sukses', 'Produk dihapus dari keranjang.');
    }
}
