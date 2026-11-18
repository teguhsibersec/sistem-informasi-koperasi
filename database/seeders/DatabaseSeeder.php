<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PengaturanSeeder::class,
            KategoriSeeder::class,
            PengurusSeeder::class,
            AnggotaSeeder::class,
            UserSeeder::class,
            PemasokSeeder::class,
            BarangSeeder::class,
            PembelianSeeder::class,
            TransaksiDummySeeder::class,
        ]);
    }
}
