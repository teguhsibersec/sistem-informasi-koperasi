<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Barang;
use App\Models\Keranjang;
use DomainException;
use Illuminate\Support\Collection;

class KeranjangService
{
    public function untuk(Anggota $anggota): Keranjang
    {
        return Keranjang::firstOrCreate(['id_anggota' => $anggota->id_anggota]);
    }

    /** Pemeriksaan stok di sini hanya "saran"; pemeriksaan yang mengikat ada di CheckoutService (row locking). */
    public function tambah(Anggota $anggota, int $idBarang, int $qty = 1): void
    {
        $barang = Barang::aktif()->findOrFail($idBarang);
        $keranjang = $this->untuk($anggota);
        $item = $keranjang->items()->where('id_barang', $idBarang)->first();
        $baru = ($item?->qty ?? 0) + $qty;

        if ($barang->stok_barang < 1) {
            throw new DomainException("Stok {$barang->nama_barang} habis.");
        }
        if ($baru > $barang->stok_barang) {
            throw new DomainException("Stok {$barang->nama_barang} hanya {$barang->stok_barang}" . ($item ? " (di keranjang sudah ada {$item->qty})." : '.'));
        }

        $item ? $item->update(['qty' => $baru]) : $keranjang->items()->create(['id_barang' => $idBarang, 'qty' => $qty]);
    }

    public function ubah(Anggota $anggota, int $idBarang, int $qty): void
    {
        if ($qty < 1) {
            $this->hapus($anggota, $idBarang);

            return;
        }

        $item = $this->untuk($anggota)->items()->where('id_barang', $idBarang)->firstOrFail();
        $barang = Barang::aktif()->find($idBarang);
        if (! $barang || $qty > $barang->stok_barang) {
            throw new DomainException('Jumlah melebihi stok yang tersedia' . ($barang ? " ({$barang->stok_barang})." : '.'));
        }

        $item->update(['qty' => $qty]);
    }

    public function hapus(Anggota $anggota, int $idBarang): void
    {
        $this->untuk($anggota)->items()->where('id_barang', $idBarang)->delete();
    }

    public function kosongkan(Anggota $anggota): void
    {
        $this->untuk($anggota)->items()->delete();
    }

    /**
     * Isi keranjang untuk ditampilkan.
     *
     * @return Collection<int, array{id_barang:int, barang:?Barang, qty:int, harga:float, subtotal:float, maks:int, tersedia:bool}>
     */
    public function isi(Anggota $anggota): Collection
    {
        return $this->untuk($anggota)->items()
            ->with(['barang' => fn ($q) => $q->withTrashed()])
            ->orderBy('id_keranjang_item')
            ->get()
            ->map(function ($item) {
                $b = $item->barang;
                $tersedia = $b && ! $b->trashed() && $b->status_barang === 'aktif' && $b->stok_barang > 0;
                $harga = $b ? (float) $b->harga_jual_barang : 0.0;

                return [
                    'id_barang' => $item->id_barang,
                    'barang' => $b,
                    'qty' => $item->qty,
                    'harga' => $harga,
                    'subtotal' => $harga * $item->qty,
                    'maks' => $tersedia ? $b->stok_barang : 0,
                    'tersedia' => $tersedia,
                ];
            });
    }

    /** Siap checkout bila tidak kosong, semua barang tersedia, dan jumlah tidak melebihi stok. */
    public function siapCheckout(Collection $isi): bool
    {
        return $isi->isNotEmpty() && $isi->every(fn ($x) => $x['tersedia'] && $x['qty'] <= $x['maks']);
    }
}
