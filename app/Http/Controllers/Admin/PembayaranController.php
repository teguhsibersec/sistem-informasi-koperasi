<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), ['menunggu', 'berhasil', 'gagal', 'kedaluwarsa'], true) ? $request->query('status') : 'menunggu';

        $pembayaran = Pembayaran::with('penjualan.anggota')
            ->where('status_pembayaran', $status)
            ->orderByDesc('id_pembayaran')
            ->paginate(15)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayaran', 'status'));
    }

    public function lunas(Request $request, Pembayaran $pembayaran, PembayaranService $layanan)
    {
        try {
            $layanan->tandaiLunas($pembayaran->id_pembayaran, $request->user()->id_pengurus);
        } catch (\DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return back()->with('sukses', 'Pembayaran QRIS ditandai lunas.');
    }

    public function cash(Request $request, Pembayaran $pembayaran, PembayaranService $layanan)
    {
        $data = $request->validate(['uang_diterima' => ['required', 'numeric', 'min:0']]);

        try {
            $layanan->konfirmasiCash($pembayaran->id_pembayaran, (float) $data['uang_diterima'], $request->user()->id_pengurus);
        } catch (\DomainException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return back()->with('sukses', 'Pembayaran cash dikonfirmasi dan pesanan selesai.');
    }
}
