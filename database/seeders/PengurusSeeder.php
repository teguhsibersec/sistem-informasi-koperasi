<?php

namespace Database\Seeders;

use App\Models\Pengurus;
use Illuminate\Database\Seeder;

class PengurusSeeder extends Seeder
{
    public function run(): void
    {
        Pengurus::updateOrCreate(['kode_pengurus' => 'BDH'], ['nama_pengurus' => 'Rina Bendahara', 'jabatan_pengurus' => 'bendahara', 'alamat_pengurus' => 'Jl. Ki Hajar Dewantara No. 5', 'kota_pengurus' => 'Metro', 'telepon_pengurus' => '081234500001']);
        Pengurus::updateOrCreate(['kode_pengurus' => 'SKR'], ['nama_pengurus' => 'Hendra Sekretaris', 'jabatan_pengurus' => 'sekretaris', 'alamat_pengurus' => 'Jl. Sudirman No. 12', 'kota_pengurus' => 'Metro', 'telepon_pengurus' => '081234500002']);
    }
}
