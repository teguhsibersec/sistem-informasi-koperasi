<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\RiwayatStok;
use App\Services\StokService;
use App\Support\Aktivitas;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public const TIPE = ['penjualan' => 'Penjualan', 'pembelian' => 'Pembelian', 'penyesuaian' => 'Penyesuaian', 'batal' => 'Pembatalan'];

    public function index(Request $request)
    {
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');
        $filter = in_array($request->query('filter'), ['menipis', 'habis'], true) ? $request->query('filter') : null;

        $barang = Barang::with('kategori')
            ->when($e !== '', fn ($x) => $x->where(fn ($w) => $w->where('kode_barang', 'like', "%{$e}%")->orWhere('nama_barang', 'like', "%{$e}%")))
            ->when($filter === 'menipis', fn ($x) => $x->stokMenipis())
            ->when($filter === 'habis', fn ($x) => $x->where('stok_barang', '<=', 0))
            ->orderBy('stok_barang')->orderBy('nama_barang')
            ->paginate(20)->withQueryString();

        return view('admin.stok.index', [
            'barang' => $barang,
            'filter' => $filter,
            'jumlahMenipis' => Barang::aktif()->stokMenipis()->count(),
        ]);
    }

    public function edit(Barang $barang)
    {
        return view('admin.stok.edit', [
            'barang' => $barang->load('kategori'),
            'riwayat' => $barang->riwayatStok()->with('pengurus')->orderByDesc('id_riwayat_stok')->limit(20)->get(),
            'tipe' => self::TIPE,
        ]);
    }

    public function update(Request $request, Barang $barang, StokService $stok)
    {
        $data = $request->validate(
            ['stok_baru' => ['required', 'integer', 'min:0', 'max:1000000'], 'alasan' => ['required', 'string', 'min:3', 'max:200']],
            ['stok_baru.required' => 'Isi jumlah stok yang sebenarnya.', 'stok_baru.min' => 'Stok tidak boleh negatif.', 'alasan.required' => 'Alasan penyesuaian wajib diisi.', 'alasan.min' => 'Alasan terlalu singkat.']
        );

        try {
            $hasil = DB::transaction(function () use ($stok, $barang, $data, $request) {
                $sebelum = Barang::lockForUpdate()->findOrFail($barang->id_barang)->stok_barang;
                $b = $stok->sesuaikan($barang->id_barang, (int) $data['stok_baru'], $data['alasan'], $request->user()->id_pengurus);
                if ($sebelum !== $b->stok_barang) {
                    Aktivitas::catat('ubah', 'barang', $b->id_barang, ['stok' => [$sebelum, $b->stok_barang], 'alasan' => $data['alasan']]);
                }

                return [$sebelum, $b->stok_barang];
            });
        } catch (DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return redirect()->route('admin.stok.edit', $barang)->with('sukses', $hasil[0] === $hasil[1]
            ? 'Stok sudah sama, tidak ada perubahan.'
            : "Stok disesuaikan dari {$hasil[0]} menjadi {$hasil[1]}.");
    }

    public function riwayat(Request $request)
    {
        $request->validate(['dari' => 'nullable|date', 'sampai' => 'nullable|date']);
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        $riwayat = RiwayatStok::with(['barang', 'pengurus'])
            ->when($e !== '', fn ($x) => $x->where(fn ($g) => $g
                ->whereHas('barang', fn ($b) => $b->withTrashed()->where(fn ($w) => $w->where('kode_barang', 'like', "%{$e}%")->orWhere('nama_barang', 'like', "%{$e}%")))
                ->orWhere('referensi', 'like', "%{$e}%")))
            ->when(array_key_exists((string) $request->query('tipe'), self::TIPE), fn ($x) => $x->where('tipe', $request->query('tipe')))
            ->when($request->filled('dari'), fn ($x) => $x->where('created_at', '>=', Carbon::parse($request->query('dari'))->startOfDay()))
            ->when($request->filled('sampai'), fn ($x) => $x->where('created_at', '<=', Carbon::parse($request->query('sampai'))->endOfDay()))
            ->orderByDesc('id_riwayat_stok')
            ->paginate(25)->withQueryString();

        return view('admin.stok.riwayat', ['riwayat' => $riwayat, 'tipe' => self::TIPE]);
    }
}
