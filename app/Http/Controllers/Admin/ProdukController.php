<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use App\Services\StokService;
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        $produk = Barang::with('kategori')
            ->when($e !== '', fn ($x) => $x->where(fn ($w) => $w->where('kode_barang', 'like', "%{$e}%")->orWhere('nama_barang', 'like', "%{$e}%")))
            ->when($request->filled('kategori'), fn ($x) => $x->where('id_kategori', (int) $request->query('kategori')))
            ->when(in_array($request->query('status'), ['aktif', 'nonaktif'], true), fn ($x) => $x->where('status_barang', $request->query('status')))
            ->when($request->boolean('menipis'), fn ($x) => $x->stokMenipis())
            ->orderBy('nama_barang')
            ->paginate(15)->withQueryString();

        return view('admin.produk.index', ['produk' => $produk, 'kategori' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function create()
    {
        return view('admin.produk.form', [
            'barang' => new Barang(['satuan_barang' => 'pcs', 'stok_min_barang' => 5, 'status_barang' => 'aktif']),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(BarangRequest $request, StokService $stok)
    {
        DB::transaction(function () use ($request, $stok) {
            $data = Arr::except($request->validated(), ['gambar', 'hapus_gambar', 'stok_awal']);
            $data['stok_barang'] = 0;
            if ($request->hasFile('gambar')) {
                $data['gambar_barang'] = $request->file('gambar')->store('produk', 'public');
            }

            $barang = Barang::create($data);

            if ((int) $request->input('stok_awal') > 0) {
                $stok->sesuaikan($barang->id_barang, (int) $request->input('stok_awal'), 'Stok awal', $request->user()->id_pengurus);
            }

            Aktivitas::catat('tambah', 'barang', $barang->id_barang, ['kode' => $barang->kode_barang, 'nama' => $barang->nama_barang]);
        });

        return redirect()->route('admin.produk.index')->with('sukses', 'Produk ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('admin.produk.form', ['barang' => $barang, 'kategori' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function update(BarangRequest $request, Barang $barang)
    {
        $data = Arr::except($request->validated(), ['gambar', 'hapus_gambar']);

        if ($request->boolean('hapus_gambar') && $barang->gambar_barang) {
            Storage::disk('public')->delete($barang->gambar_barang);
            $data['gambar_barang'] = null;
        }
        if ($request->hasFile('gambar')) {
            if ($barang->gambar_barang) {
                Storage::disk('public')->delete($barang->gambar_barang);
            }
            $data['gambar_barang'] = $request->file('gambar')->store('produk', 'public');
        }

        $barang->update($data);
        Aktivitas::catat('ubah', 'barang', $barang->id_barang, Arr::except($barang->getChanges(), ['updated_at']));

        return redirect()->route('admin.produk.index')->with('sukses', 'Produk diperbarui.');
    }

    /** Soft delete: riwayat transaksi dan laporan tetap utuh. */
    public function destroy(Barang $barang)
    {
        $barang->delete();
        Aktivitas::catat('hapus', 'barang', $barang->id_barang, ['kode' => $barang->kode_barang]);

        return redirect()->route('admin.produk.index')->with('sukses', 'Produk dihapus.');
    }
}
