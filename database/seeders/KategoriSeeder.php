<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Alat Tulis (ATK)', 'atk', 'notebook'],
            ['Makanan & Minuman', 'makanan-minuman', 'cup'],
            ['Perlengkapan Sekolah', 'perlengkapan-sekolah', 'bag'],
            ['Merchandise', 'merchandise', 'shirt'],
            ['Lainnya', 'lainnya', 'grid'],
        ];

        foreach ($data as [$nama, $slug, $ikon]) {
            Kategori::updateOrCreate(['slug_kategori' => $slug], ['nama_kategori' => $nama, 'ikon' => $ikon]);
        }
    }
}
