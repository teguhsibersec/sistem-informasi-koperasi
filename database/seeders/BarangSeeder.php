<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $kat = Kategori::pluck('id_kategori', 'slug_kategori');

        // [kode, nama, slug kategori (jenis barang), harga jual]
        $data = [
            ['ATK-001', 'Buku Tulis A5 100 Lembar', 'atk', 8000],
            ['ATK-002', 'Pulpen Gel Hitam', 'atk', 5000],
            ['MKN-001', 'Air Mineral 600ml', 'makanan-minuman', 4000],
            ['MKN-002', 'Snack Rasa Cokelat', 'makanan-minuman', 7000],
            ['MRC-001', 'Tas Kain Sekolah', 'merchandise', 35000],
            ['ATK-003', 'Notebook Spiral', 'atk', 18000],
            ['ATK-004', 'Stabilo Highlighter', 'atk', 12000],
            ['MKN-003', 'Mie Instan', 'makanan-minuman', 6000],
            ['ATK-005', 'Penghapus', 'atk', 3000],
            ['ATK-006', 'Pensil 2B', 'atk', 3000],
            ['ATK-007', 'Penggaris 30cm', 'atk', 5000],
            ['ATK-008', 'Tipe-X Cair', 'atk', 6000],
            ['ATK-009', 'Spidol Papan Tulis', 'atk', 9000],
            ['ATK-010', 'Map Plastik', 'atk', 2500],
            ['ATK-011', 'Kertas HVS A4 (1 Rim)', 'atk', 55000],
            ['ATK-012', 'Stapler Kecil', 'atk', 15000],
            ['ATK-013', 'Lem Stick', 'atk', 7000],
            ['ATK-014', 'Gunting', 'atk', 10000],
            ['MKN-004', 'Teh Botol', 'makanan-minuman', 5000],
            ['MKN-005', 'Kopi Sachet', 'makanan-minuman', 3000],
            ['MKN-006', 'Roti Cokelat', 'makanan-minuman', 6000],
            ['MKN-007', 'Wafer Krim', 'makanan-minuman', 5000],
            ['MKN-008', 'Susu Kotak', 'makanan-minuman', 6000],
            ['PKP-001', 'Flashdisk 32GB', 'perlengkapan-sekolah', 65000],
            ['PKP-002', 'Sampul Buku', 'perlengkapan-sekolah', 12000],
            ['PKP-003', 'Kalkulator Scientific', 'perlengkapan-sekolah', 120000],
            ['MRC-002', 'Kaos Sekolah', 'merchandise', 85000],
            ['MRC-003', 'Gantungan Kunci Sekolah', 'merchandise', 10000],
            ['MRC-004', 'Tumbler Sekolah', 'merchandise', 45000],
            ['LNY-001', 'Payung Lipat', 'lainnya', 30000],
        ];

        $satuan = fn (string $n) => match (true) {
            str_contains($n, 'Air Mineral') || str_contains($n, 'Teh Botol') => 'botol',
            str_contains($n, 'Susu Kotak') => 'kotak',
            str_contains($n, 'Mie') || str_contains($n, 'Snack') || str_contains($n, 'Wafer') => 'bungkus',
            str_contains($n, 'Kopi Sachet') => 'sachet',
            str_contains($n, 'HVS') => 'rim',
            default => 'pcs',
        };

        foreach ($data as [$kode, $nama, $slug, $harga]) {
            Barang::updateOrCreate(['kode_barang' => $kode], [
                'nama_barang' => $nama,
                'satuan_barang' => $satuan($nama),
                'id_kategori' => $kat[$slug],
                'harga_jual_barang' => $harga,
                'stok_barang' => 0,
                'stok_min_barang' => 5,
                'deskripsi_barang' => $nama . ' - tersedia di Koperasi Sekolah SMP Negeri 2 Metro.',
                'status_barang' => 'aktif',
            ]);
        }
    }
}
