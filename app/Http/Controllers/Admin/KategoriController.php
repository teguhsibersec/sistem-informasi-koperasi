<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /** Ikon yang tersedia di komponen <x-ikon>. */
    public const IKON = ['notebook' => 'Alat Tulis', 'cup' => 'Makanan & Minuman', 'bag' => 'Tas / Perlengkapan', 'shirt' => 'Pakaian / Merchandise', 'grid' => 'Umum'];

    public function index()
    {
        return view('admin.kategori.index', [
            'kategori' => Kategori::withCount('barang')->orderBy('nama_kategori')->get(),
            'daftarIkon' => self::IKON,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $kategori = Kategori::create($data + ['slug_kategori' => $this->slugUnik($data['nama_kategori'])]);
        Aktivitas::catat('tambah', 'kategori', $kategori->id_kategori, ['nama' => $kategori->nama_kategori]);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', ['kategori' => $kategori, 'daftarIkon' => self::IKON]);
    }

    /** Slug tidak ikut berubah agar tautan toko tetap valid. */
    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($request->validate($this->aturan($kategori)));
        Aktivitas::catat('ubah', 'kategori', $kategori->id_kategori, $kategori->getChanges());

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        $dipakai = $kategori->barang()->withTrashed()->count();   // FK RESTRICT juga menghitung produk yang sudah dihapus
        if ($dipakai > 0) {
            return back()->with('galat', "Kategori masih dipakai {$dipakai} produk (termasuk yang sudah dihapus) dan tidak dapat dihapus.");
        }

        $kategori->delete();
        Aktivitas::catat('hapus', 'kategori', $kategori->id_kategori, ['nama' => $kategori->nama_kategori]);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori dihapus.');
    }

    private function aturan(?Kategori $kategori = null): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:50', Rule::unique('kategori', 'nama_kategori')->ignore($kategori?->id_kategori, 'id_kategori')],
            'ikon' => ['required', Rule::in(array_keys(self::IKON))],
        ];
    }

    private function slugUnik(string $nama): string
    {
        $dasar = Str::slug($nama) ?: 'kategori';
        $slug = $dasar;
        for ($i = 2; Kategori::withTrashed()->where('slug_kategori', $slug)->exists(); $i++) {
            $slug = "{$dasar}-{$i}";
        }

        return $slug;
    }
}
