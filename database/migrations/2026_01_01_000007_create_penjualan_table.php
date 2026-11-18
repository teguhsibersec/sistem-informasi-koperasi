<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $t) {
            $t->increments('id_penjualan');
            $t->char('no_nota_penjualan', 12)->unique();
            $t->dateTime('tgl_penjualan');
            $t->unsignedSmallInteger('id_pengurus')->nullable(); // online: kosong sampai diproses
            $t->unsignedInteger('id_anggota')->nullable();      // null = pembeli umum
            $t->decimal('bayar_penjualan', 12, 2)->default(0);
            $t->enum('sumber_penjualan', ['kasir', 'online'])->default('kasir');
            $t->enum('status_penjualan', ['menunggu_bayar', 'diproses', 'siap_diambil', 'selesai', 'batal'])->default('menunggu_bayar');
            $t->string('catatan_penjualan', 255)->nullable();
            $t->timestamps();

            $t->foreign('id_pengurus')->references('id_pengurus')->on('pengurus')->restrictOnDelete();
            $t->foreign('id_anggota')->references('id_anggota')->on('anggota')->restrictOnDelete();
            $t->index('tgl_penjualan');
            $t->index('status_penjualan');
            $t->index(['id_anggota', 'tgl_penjualan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
