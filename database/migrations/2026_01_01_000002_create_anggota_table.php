<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $t) {
            $t->increments('id_anggota');
            $t->char('no_anggota', 6)->unique();
            $t->string('nis_anggota', 20)->unique();
            $t->string('nama_anggota', 100);
            $t->string('kelas_anggota', 15);
            $t->string('alamat_anggota', 100)->nullable();
            $t->string('kota_anggota', 35)->nullable();
            $t->string('no_hp_anggota', 15)->nullable();  // Telepon
            $t->enum('status_anggota', ['aktif', 'nonaktif'])->default('aktif');
            $t->date('tgl_daftar_anggota');
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
