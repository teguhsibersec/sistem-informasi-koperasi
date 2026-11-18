<?php

namespace Database\Seeders;

use App\Models\Pemasok;
use Illuminate\Database\Seeder;

class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['CV Sinar Alat Tulis', '0725-41234', 'Jl. Jend. Sudirman No. 12, Metro, Lampung'],
            ['UD Berkah Pangan', '0725-42345', 'Jl. Ki Hajar Dewantara No. 45, Metro, Lampung'],
            ['Kreasi Merch Lampung', '0721-53456', 'Jl. Teuku Umar No. 8, Bandar Lampung'],
        ];

        foreach ($data as [$n, $t, $a]) {
            Pemasok::updateOrCreate(['nama_pemasok' => $n], ['telepon_pemasok' => $t, 'alamat_pemasok' => $a]);
        }
    }
}
