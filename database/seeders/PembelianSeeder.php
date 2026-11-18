<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Pemasok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembelianSeeder extends Seeder
{
    /** 3 pembelian 'diterima' (mengisi stok awal 150/barang) + 1 'dipesan' untuk demo gudang. */
    public function run(): void
    {
        $pemasok = Pemasok::orderBy('id_pemasok')->get();
        $barang = Barang::orderBy('id_barang')->get();
        $gudang = DB::table('pengurus')->where('kode_pengurus', 'SKR')->value('id_pengurus');

        foreach ($pemasok as $i => $p) {
            $tgl = now()->subDays(30 - $i * 3);
            $id = DB::table('pembelian')->insertGetId([
                'no_faktur_pembelian' => sprintf('FKT-%s-%03d', $tgl->format('ymd'), $i + 1),
                'tgl_pembelian' => $tgl->toDateString(),
                'id_pemasok' => $p->id_pemasok,
                'status_pembelian' => 'diterima',
                'created_at' => $tgl, 'updated_at' => $tgl,
            ]);

            foreach ($barang->filter(fn ($b, $k) => $k % 3 === $i) as $b) {
                DB::table('detail_pembelian')->insert([
                    'id_pembelian' => $id,
                    'id_barang' => $b->id_barang,
                    'qty_detail_pembelian' => 150,
                    'harga_beli_detail_pembelian' => round($b->harga_jual_barang * 0.75 / 500) * 500,
                ]);
                DB::table('barang')->where('id_barang', $b->id_barang)->update(['stok_barang' => 150]);
                DB::table('riwayat_stok')->insert([
                    'id_barang' => $b->id_barang, 'tipe' => 'pembelian', 'qty_perubahan' => 150,
                    'stok_sebelum' => 0, 'stok_sesudah' => 150,
                    'referensi' => sprintf('FKT-%s-%03d', $tgl->format('ymd'), $i + 1),
                    'id_pengurus' => $gudang, 'created_at' => $tgl,
                ]);
            }
        }

        // Pesanan yang belum diterima (stok belum bertambah)
        $id = DB::table('pembelian')->insertGetId([
            'no_faktur_pembelian' => 'FKT-' . now()->format('ymd') . '-099',
            'tgl_pembelian' => now()->toDateString(),
            'id_pemasok' => $pemasok->first()->id_pemasok,
            'status_pembelian' => 'dipesan',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach ($barang->whereIn('kode_barang', ['ATK-001', 'ATK-002', 'ATK-005']) as $b) {
            DB::table('detail_pembelian')->insert([
                'id_pembelian' => $id, 'id_barang' => $b->id_barang,
                'qty_detail_pembelian' => 100,
                'harga_beli_detail_pembelian' => round($b->harga_jual_barang * 0.75 / 500) * 500,
            ]);
        }
    }
}
