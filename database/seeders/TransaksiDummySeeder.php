<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiDummySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $kasir = DB::table('pengurus')->where('kode_pengurus', 'BDH')->value('id_pengurus');
        $anggota = DB::table('anggota')->where('status_anggota', 'aktif')->pluck('id_anggota')->all();
        $barang = DB::table('barang')->get()->keyBy('id_barang');
        $ids = $barang->keys()->all();
        $seq = [];

        for ($i = 0; $i < 100; $i++) {
            $hari = $i < 14 ? 0 : random_int(1, 13);   // 14 transaksi hari ini
            $waktu = $now->copy()->subDays($hari)->setTime(random_int(8, 15), random_int(0, 59), random_int(0, 59));
            if ($waktu->gt($now)) {
                $waktu = $now->copy()->subMinutes(random_int(5, 60));
            }

            $online = random_int(1, 100) <= 30;
            $metode = random_int(1, 100) <= 55 ? 'qris' : 'cash';
            $status = 'selesai';
            if ($online) {
                if ($hari === 0) {
                    $status = ['diproses', 'menunggu_bayar', 'selesai'][array_rand([0, 1, 2])];
                } elseif (random_int(1, 100) <= 8) {
                    $status = 'batal';
                }
            }
            if ($status === 'menunggu_bayar') {
                $metode = 'qris';
                $waktu = $now->copy()->subMinutes(random_int(1, 10));
            }
            if ($status === 'batal') {
                $metode = 'qris';
            }

            // item
            $lines = [];
            foreach ((array) array_rand(array_flip($ids), random_int(1, 3)) as $idb) {
                $lines[$idb] = random_int(1, 3);
            }
            $total = 0;
            foreach ($lines as $idb => $q) {
                $total += $q * $barang[$idb]->harga_jual_barang;
            }

            // pembayaran
            $paid = in_array($status, ['selesai']) || ($status === 'diproses' && $metode === 'qris');
            $diterima = $metode === 'cash' ? ceil($total / 5000) * 5000 : null;
            $bayar = $paid ? ($metode === 'cash' ? $diterima : $total) : 0;

            $tgl = $waktu->format('ymd');
            $nota = 'KP' . $tgl . sprintf('%04d', $seq[$tgl] = ($seq[$tgl] ?? 0) + 1);

            $idp = DB::table('penjualan')->insertGetId([
                'no_nota_penjualan' => $nota,
                'tgl_penjualan' => $waktu,
                'id_pengurus' => (! $online || $status === 'selesai') ? $kasir : null,
                'id_anggota' => $online ? $anggota[array_rand($anggota)] : (random_int(1, 100) <= 60 ? $anggota[array_rand($anggota)] : null),
                'bayar_penjualan' => $bayar,
                'sumber_penjualan' => $online ? 'online' : 'kasir',
                'status_penjualan' => $status,
                'created_at' => $waktu, 'updated_at' => $waktu,
            ]);

            foreach ($lines as $idb => $q) {
                DB::table('detail_penjualan')->insert([
                    'id_penjualan' => $idp, 'id_barang' => $idb,
                    'qty_detail_penjualan' => $q,
                    'harga_satuan_detail_penjualan' => $barang[$idb]->harga_jual_barang,
                ]);

                if ($status !== 'batal') {   // stok dikurangi saat checkout/penjualan
                    $sebelum = DB::table('barang')->where('id_barang', $idb)->value('stok_barang');
                    DB::table('barang')->where('id_barang', $idb)->update(['stok_barang' => $sebelum - $q]);
                    DB::table('riwayat_stok')->insert([
                        'id_barang' => $idb, 'tipe' => 'penjualan', 'qty_perubahan' => -$q,
                        'stok_sebelum' => $sebelum, 'stok_sesudah' => $sebelum - $q,
                        'referensi' => $nota, 'id_pengurus' => $kasir, 'created_at' => $waktu,
                    ]);
                }
            }

            $bayarStatus = $paid ? 'berhasil' : ($status === 'batal' ? 'kedaluwarsa' : 'menunggu');
            DB::table('pembayaran')->insert([
                'id_penjualan' => $idp,
                'metode_pembayaran' => $metode,
                'jumlah_tagihan' => $total,
                'uang_diterima' => $paid ? $diterima : null,
                'kembalian' => $paid && $diterima ? $diterima - $total : null,
                'status_pembayaran' => $bayarStatus,
                'kode_referensi' => $metode === 'qris' ? 'QRIS' . strtoupper(Str::random(12)) : null,
                'dibayar_pada' => $paid ? $waktu->copy()->addMinutes(random_int(0, 5)) : null,
                'kedaluwarsa_pada' => $metode === 'qris' ? $waktu->copy()->addMinutes(15) : null,
                'dikonfirmasi_oleh' => $paid ? $kasir : null,
                'created_at' => $waktu, 'updated_at' => $waktu,
            ]);
        }

        // Stok opname: samakan panel "Stok Menipis" dengan screenshot (min 10 supaya Penghapus stok 10 tampil)
        $final = ['MKN-001' => 3, 'MKN-002' => 5, 'ATK-001' => 7, 'ATK-002' => 8, 'ATK-005' => 10];
        foreach ($final as $kode => $stok) {
            $b = DB::table('barang')->where('kode_barang', $kode)->first();
            DB::table('barang')->where('id_barang', $b->id_barang)->update(['stok_barang' => $stok, 'stok_min_barang' => 10]);
            DB::table('riwayat_stok')->insert([
                'id_barang' => $b->id_barang, 'tipe' => 'penyesuaian', 'qty_perubahan' => $stok - $b->stok_barang,
                'stok_sebelum' => $b->stok_barang, 'stok_sesudah' => $stok,
                'catatan' => 'Stok opname awal (seeder)', 'created_at' => $now,
            ]);
        }
    }
}
