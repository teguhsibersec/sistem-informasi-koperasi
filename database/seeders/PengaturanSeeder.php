<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use App\Services\Qris\QrisCrc16;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'nama_koperasi' => config('koperasi.nama'),
            'tagline' => config('koperasi.subjudul'),
            'alamat' => config('koperasi.alamat'),
            'jam_layanan' => '08.00-16.00',
            'nama_merchant_qris' => config('koperasi.nama'),
            // QRIS DEMO (bukan merchant asli, tidak bisa dibayar) agar alur kasir bisa dicoba.
            // Ganti dengan string QRIS statis koperasi (diawali 000201...) di menu Pengaturan.
            'qris_statis' => self::qrisDemo(),
            'qris_masa_berlaku_menit' => '15',
            'qris_provider' => config('qris.provider', 'manual'), // manual | gateway
        ];

        foreach ($data as $k => $v) {
            Pengaturan::updateOrCreate(['kunci' => $k], ['nilai' => $v]);
        }
    }

    private static function qrisDemo(): string
    {
        $isi = '00020101021126220007ID.DEMO0107DEMO0015204541153033605802ID5908KOPERASI6005METRO';
        $isi .= '6304';

        return $isi . QrisCrc16::hitung($isi);
    }
}
