<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pengaturan;
use App\Services\CheckoutService;
use App\Services\KeranjangService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private KeranjangService $keranjang)
    {
    }

    public function create(Request $request)
    {
        $isi = $this->keranjang->isi($this->anggota($request));

        if ($request->filled('items')) {
            $ids = array_map('intval', array_filter(explode(',', (string) $request->query('items')), 'is_numeric'));
            if (! empty($ids)) {
                $isi = $isi->filter(fn ($x) => in_array((int) $x['id_barang'], $ids, true))->values();
            }
        }

        if (! $this->keranjang->siapCheckout($isi)) {
            return redirect()->route('toko.keranjang')->with('galat', 'Periksa keranjang Anda: kosong, ada barang tidak tersedia, atau jumlahnya melebihi stok.');
        }

        return view('toko.checkout', [
            'isi' => $isi,
            'total' => $isi->sum('subtotal'),
            'menit' => (int) Pengaturan::ambil('qris_masa_berlaku_menit', 15),
            'jamLayanan' => Pengaturan::ambil('jam_layanan', '08.00-16.00'),
            'selectedItems' => $request->query('items'),
        ]);
    }

    public function store(Request $request, CheckoutService $checkout)
    {
        $d = $request->validate([
            'metode' => ['required', 'in:cash,qris'],
            'catatan' => ['nullable', 'string', 'max:200'],
            'items' => ['nullable', 'string'],
        ], ['metode.required' => 'Pilih metode pembayaran.', 'metode.in' => 'Pilih metode pembayaran.']);

        $anggota = $this->anggota($request);
        $isi = $this->keranjang->isi($anggota);

        if ($request->filled('items')) {
            $ids = array_map('intval', array_filter(explode(',', (string) $request->input('items')), 'is_numeric'));
            if (! empty($ids)) {
                $isi = $isi->filter(fn ($x) => in_array((int) $x['id_barang'], $ids, true))->values();
            }
        }

        if (! $this->keranjang->siapCheckout($isi)) {
            return redirect()->route('toko.keranjang')->with('galat', 'Periksa keranjang Anda sebelum checkout.');
        }

        try {
            // Pesanan + pengosongan keranjang item terpilih satu transaksi: gagal di mana pun, keranjang tetap utuh.
            $penjualan = DB::transaction(function () use ($checkout, $isi, $d, $anggota) {
                $p = $checkout->proses([
                    'items' => $isi->mapWithKeys(fn ($x) => [$x['id_barang'] => $x['qty']])->all(),
                    'metode' => $d['metode'],
                    'sumber' => 'online',
                    'id_anggota' => $anggota->id_anggota,
                    'id_pengurus' => null,
                    'catatan' => $d['catatan'] ?? null,
                ]);

                foreach ($isi as $item) {
                    $this->keranjang->hapus($anggota, (int) $item['id_barang']);
                }

                return $p;
            });
        } catch (DomainException $e) {
            return redirect()->route('toko.keranjang')->with('galat', $e->getMessage());
        }

        return $d['metode'] === 'qris'
            ? redirect()->route('toko.pesanan.bayar', $penjualan)
            : redirect()->route('toko.pesanan.show', $penjualan)->with('sukses', 'Pesanan dibuat. Bayar tunai saat mengambil pesanan di koperasi.');
    }

    private function anggota(Request $request): Anggota
    {
        return $request->user()->anggota ?? abort(403);
    }
}
