<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'admin', 'email' => 'admin@koperasi.test', 'password' => 'password', 'role' => 'admin',
        ]);

        User::updateOrCreate(['username' => 'kasir'], [
            'name' => 'Rina Bendahara', 'email' => 'kasir@koperasi.test', 'password' => 'password', 'role' => 'pengurus',
            'id_pengurus' => Pengurus::where('kode_pengurus', 'BDH')->value('id_pengurus'),
        ]);

        User::updateOrCreate(['username' => 'gudang'], [
            'name' => 'Hendra Sekretaris', 'email' => 'gudang@koperasi.test', 'password' => 'password', 'role' => 'pengurus',
            'id_pengurus' => Pengurus::where('kode_pengurus', 'SKR')->value('id_pengurus'),
        ]);

        $a = Anggota::where('no_anggota', 'A00001')->first();
        User::updateOrCreate(['username' => 'anggota'], [
            'name' => $a->nama_anggota, 'email' => 'anggota@koperasi.test', 'password' => 'password',
            'role' => 'anggota', 'id_anggota' => $a->id_anggota,
        ]);
    }
}
