<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Pemasok;
use App\Services\PembelianService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PembelianController extends Controller
{
    public function __construct(private PembelianService $layanan)
    {
    }

    public function index(Request $request)
    {
        $pembelian = Pembelian::with(['pemasok', 'details'])
            ->when(in_array($request->query('status'), ['dipesan', 'diterima'], true), fn ($x) => $x->where('status_pembelian', $request->query('status')))
            ->when($request->filled('pemasok'), fn ($x) => $x->where('id_pemasok', (int) $request->query('pemasok')))
            ->orderByDesc('tgl_pembelian')->orderByDesc('id_pembelian')
            ->paginate(15)->withQueryString();

        return view('admin.pembelian.index', ['pembelian' => $pembelian, 'pemasok' => Pemasok::orderBy('nama_pemasok')->get()]);
    }

    public function create()
    {
        return view('admin.pembelian.create', [
            'pemasok' => Pemasok::orderBy('nama_pemasok')->get(),
            'barang' => Barang::orderBy('nama_barang')->get(['id_barang', 'kode_barang', 'nama_barang'])
                ->map(fn ($b) => ['id' => $b->id_barang, 'label' => "{$b->kode_barang} - {$b->nama_barang}"])->values(),
            'faktur' => 'FKT-' . now()->format('ymd') . '-' . sprintf('%03d', Pembelian::whereDate('tgl_pembelian', today())->count() + 1),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pemasok' => ['required', 'integer', Rule::exists('pemasok', 'id_pemasok')->whereNull('deleted_at')],
            'no_faktur_pembelian' => ['required', 'string', 'max:20', Rule::unique('pembelian', 'no_faktur_pembelian')->where('id_pemasok', $request->input('id_pemasok'))],
            'tgl_pembelian' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.id_barang' => ['required', 'integer', 'distinct', Rule::exists('barang', 'id_barang')->whereNull('deleted_at')],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.harga' => ['required', 'numeric', 'min:0', 'max:9999999999'],
        ], [
            'no_faktur_pembelian.unique' => 'No. faktur ini sudah tercatat untuk pemasok yang sama.',
            'items.required' => 'Tambahkan minimal satu barang.',
            'items.min' => 'Tambahkan minimal satu barang.',
            'items.*.id_barang.required' => 'Pilih barang pada setiap baris.',
            'items.*.id_barang.distinct' => 'Barang yang sama tidak boleh muncul dua kali. Gabungkan jumlahnya.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
            'items.*.harga.required' => 'Isi harga beli pada setiap baris.',
        ]);

        $pembelian = $this->layanan->buat((int) $data['id_pemasok'], $data['no_faktur_pembelian'], $data['tgl_pembelian'], $data['items']);

        return redirect()->route('admin.pembelian.show', $pembelian)->with('sukses', 'Pembelian dicatat dengan status dipesan. Konfirmasi saat barang tiba.');
    }

    public function show(Pembelian $pembelian)
    {
        return view('admin.pembelian.show', ['pembelian' => $pembelian->load(['pemasok', 'details.barang'])]);
    }

    public function terima(Request $request, Pembelian $pembelian)
    {
        $this->layanan->terima($pembelian->id_pembelian, $request->user()->id_pengurus);

        return back()->with('sukses', 'Barang diterima. Stok bertambah dan tercatat di riwayat stok.');
    }

    public function destroy(Pembelian $pembelian)
    {
        try {
            $this->layanan->hapus($pembelian->id_pembelian);
        } catch (DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return redirect()->route('admin.pembelian.index')->with('sukses', 'Pembelian dihapus.');
    }
}
