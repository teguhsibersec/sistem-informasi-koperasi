<?php

namespace App\Support;

use App\Models\User;

class Menu
{
    /** Menu sidebar yang boleh dilihat pengguna. [label, rute, pola aktif, ikon, peran] */
    public static function untuk(?User $user): array
    {
        $semua = [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard', 'dashboard', ['admin']],
            ['Produk', 'admin.produk.index', 'admin.produk.*', 'produk', ['admin', 'gudang']],
            ['Kategori', 'admin.kategori.index', 'admin.kategori.*', 'kategori', ['admin', 'gudang']],
            ['Stok Barang', 'admin.stok.index', 'admin.stok.*', 'stok', ['admin', 'gudang']],
            ['Transaksi', 'admin.transaksi.index', 'admin.transaksi.*', 'transaksi', ['admin', 'kasir']],
            ['Data Anggota', 'admin.anggota.index', 'admin.anggota.*', 'anggota', ['admin']],
            ['Data Pengurus', 'admin.pengurus.index', 'admin.pengurus.*', 'anggota', ['admin']],
            ['Laporan', 'admin.laporan.index', 'admin.laporan.*', 'laporan', ['admin']],
            ['Pengaturan', 'admin.pengaturan.index', 'admin.pengaturan.*', 'pengaturan', ['admin']],
            ['Kasir (POS)', 'kasir.pos', 'kasir.*', 'kasir', ['admin', 'kasir']],
            ['Pembelian / Pemasok', 'admin.pembelian.index', 'admin.pembelian.*', 'pembelian', ['admin', 'gudang']],
            ['Pembayaran', 'admin.pembayaran.index', 'admin.pembayaran.*', 'pembayaran', ['admin', 'kasir']],
        ];

        $hasil = [];
        foreach ($semua as [$label, $rute, $aktif, $ikon, $peran]) {
            if (Akses::punyaSalahSatu($user, $peran)) {
                $hasil[] = compact('label', 'rute', 'aktif', 'ikon');
            }
        }

        return $hasil;
    }
}
