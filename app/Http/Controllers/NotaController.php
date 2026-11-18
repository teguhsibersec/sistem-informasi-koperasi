<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaController extends Controller
{
    public function show(Penjualan $penjualan)
    {
        return view('nota.struk', $this->data($penjualan, false));
    }

    public function pdf(Penjualan $penjualan)
    {
        $data = $this->data($penjualan, true);
        $tinggi = 260 + $penjualan->details->count() * 34; // pt; kertas 58mm = 164,4pt

        return Pdf::loadView('nota.struk', $data)
            ->setPaper([0, 0, 164.4, $tinggi])
            ->stream('nota-' . $penjualan->no_nota_penjualan . '.pdf');
    }

    private function data(Penjualan $penjualan, bool $pdf): array
    {
        $penjualan->load(['details.barang', 'anggota', 'pengurus', 'pembayaranTerakhir']);

        return [
            'penjualan' => $penjualan,
            'pembayaran' => $penjualan->pembayaranTerakhir,
            'koperasi' => Pengaturan::ambil('nama_koperasi', config('koperasi.nama')),
            'alamat' => Pengaturan::ambil('alamat', config('koperasi.alamat')),
            'pdf' => $pdf,
        ];
    }
}
