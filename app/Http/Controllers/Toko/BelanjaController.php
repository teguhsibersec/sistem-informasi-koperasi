<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BelanjaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $slug = trim((string) $request->query('kategori', ''));
        $e = addcslashes($q, '%_\\');
        $kategoriAktif = $slug !== '' ? Kategori::where('slug_kategori', $slug)->first() : null;

        $produk = Barang::aktif()
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w
                ->where('nama_barang', 'like', "%{$e}%")
                ->orWhere('kode_barang', 'like', "%{$e}%")
                ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%{$e}%"))))
            ->when($kategoriAktif, fn ($x) => $x->where('id_kategori', $kategoriAktif->id_kategori))
            ->when($slug !== '' && ! $kategoriAktif, fn ($x) => $x->whereRaw('1 = 0'))
            ->orderBy('nama_barang')
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('toko._produk', compact('produk', 'q'));
        }

        return view('toko.belanja', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('id_kategori')->get(),
            'kategoriAktif' => $kategoriAktif,
            'q' => $q,
        ]);
    }

    public function show(Barang $barang)
    {
        abort_unless($barang->status_barang === 'aktif', 404);

        return view('toko.produk', [
            'barang' => $barang->load('kategori'),
            'terkait' => Barang::aktif()->where('id_kategori', $barang->id_kategori)->where('id_barang', '!=', $barang->id_barang)->limit(4)->get(),
        ]);
    }
}
