<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pengaturan;

class BerandaController extends Controller
{
    public function index()
    {
        return view('toko.beranda', [
            'kategori' => Kategori::orderBy('id_kategori')->get(),
            'terbaru' => Barang::aktif()->orderByDesc('created_at')->orderByDesc('id_barang')->limit(8)->get(),
            'jamLayanan' => Pengaturan::ambil('jam_layanan', '08.00-16.00'),
        ]);
    }

    public function tentang()
    {
        return view('toko.tentang', [
            'nama' => Pengaturan::ambil('nama_koperasi', config('koperasi.nama')),
            'alamat' => Pengaturan::ambil('alamat', config('koperasi.alamat')),
            'jamLayanan' => Pengaturan::ambil('jam_layanan', '08.00-16.00'),
        ]);
    }
}
