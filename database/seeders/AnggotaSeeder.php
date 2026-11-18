<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('id_ID');
        $kelas = ['VII-A', 'VII-B', 'VII-C', 'VIII-A', 'VIII-B', 'VIII-C', 'IX-A', 'IX-B', 'IX-C'];

        $nama = ['Andi Pratama', 'Siti Nurhaliza', 'Budi Santoso', 'Rani Oktavia',
                 'Dimas Arya', 'Eka Putri', 'Fajar Maulana', 'Laila Zahra'];
        while (count($nama) < 20) {
            $nama[] = $faker->name();
        }

        foreach ($nama as $i => $n) {
            $no = $i + 1;
            Anggota::updateOrCreate(['no_anggota' => sprintf('A%05d', $no)], [
                'nis_anggota' => '2201' . sprintf('%06d', $no),
                'nama_anggota' => $n,
                'kelas_anggota' => $kelas[array_rand($kelas)],
                'alamat_anggota' => $faker->streetAddress(),
                'kota_anggota' => 'Metro',
                'no_hp_anggota' => '08' . random_int(1000000000, 9999999999) % 10000000000,
                'status_anggota' => $no > 18 ? 'nonaktif' : 'aktif',
                'tgl_daftar_anggota' => now()->subDays(random_int(10, 700))->toDateString(),
            ]);
        }
    }
}
