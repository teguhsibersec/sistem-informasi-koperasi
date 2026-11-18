<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public const STATUS = [
        'menunggu_bayar' => 'Menunggu Bayar', 'diproses' => 'Diproses', 'siap_diambil' => 'Siap Diambil',
        'selesai' => 'Selesai', 'batal' => 'Batal',
    ];

    public function index(Request $request)
    {
        $request->validate(['dari' => 'nullable|date', 'sampai' => 'nullable|date']);

        $transaksi = Penjualan::with(['anggota', 'details', 'pembayaranTerakhir'])
            ->when($request->filled('q'), fn ($x) => $x->where('no_nota_penjualan', 'like', '%' . addcslashes($request->q, '%_\\') . '%'))
            ->when($request->filled('dari'), fn ($x) => $x->where('tgl_penjualan', '>=', Carbon::parse($request->dari)->startOfDay()))
            ->when($request->filled('sampai'), fn ($x) => $x->where('tgl_penjualan', '<=', Carbon::parse($request->sampai)->endOfDay()))
            ->when(array_key_exists((string) $request->status, self::STATUS), fn ($x) => $x->where('status_penjualan', $request->status))
            ->when(in_array($request->metode, ['cash', 'qris'], true), fn ($x) => $x->whereHas('pembayaran', fn ($y) => $y->where('metode_pembayaran', $request->metode)))
            ->orderByDesc('tgl_penjualan')->orderByDesc('id_penjualan')
            ->paginate(15)->withQueryString();

        return view('admin.transaksi.index', ['transaksi' => $transaksi, 'daftarStatus' => self::STATUS]);
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['details.barang', 'anggota', 'pengurus', 'pembayaran']);

        return view('admin.transaksi.show', ['penjualan' => $penjualan]);
    }

    public function batal(Request $request, Penjualan $penjualan, PenjualanService $layanan)
    {
        $data = $request->validate(['alasan' => ['required', 'string', 'max:200']]);

        $layanan->batalkan($penjualan->id_penjualan, $request->user()->id_pengurus, $data['alasan']);

        return redirect()->route('admin.transaksi.show', $penjualan)->with('sukses', 'Transaksi dibatalkan dan stok dikembalikan.');
    }

    /** Pesanan online: diproses -> siap_diambil -> selesai. 'selesai' mensyaratkan pembayaran berhasil. */
    public function status(Request $request, Penjualan $penjualan)
    {
        $data = $request->validate(['status' => ['required', 'in:siap_diambil,selesai']]);
        $bayar = $penjualan->pembayaranTerakhir()->first();

        if ($penjualan->sumber_penjualan !== 'online' || ! in_array($penjualan->status_penjualan, ['diproses', 'siap_diambil'], true)) {
            return back()->with('galat', 'Status pesanan ini tidak dapat diubah dari sini.');
        }
        if ($data['status'] === 'selesai' && $bayar?->status_pembayaran !== 'berhasil') {
            return back()->with('galat', 'Konfirmasi pembayaran dulu di menu Pembayaran sebelum menyelesaikan pesanan.');
        }

        $sebelum = $penjualan->status_penjualan;
        $penjualan->update(['status_penjualan' => $data['status'], 'id_pengurus' => $penjualan->id_pengurus ?? $request->user()->id_pengurus]);

        LogAktivitas::create([
            'user_id' => $request->user()->id, 'aksi' => 'ubah', 'tabel' => 'penjualan', 'id_rekaman' => (string) $penjualan->id_penjualan,
            'perubahan' => ['status' => [$sebelum, $data['status']]],
        ]);

        return back()->with('sukses', 'Status pesanan diperbarui.');
    }
}
