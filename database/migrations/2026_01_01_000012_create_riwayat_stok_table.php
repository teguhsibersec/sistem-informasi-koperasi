<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_stok', function (Blueprint $t) {
            $t->bigIncrements('id_riwayat_stok');
            $t->unsignedInteger('id_barang');
            $t->enum('tipe', ['penjualan', 'pembelian', 'penyesuaian', 'batal']);
            $t->integer('qty_perubahan');
            $t->integer('stok_sebelum');
            $t->integer('stok_sesudah');
            $t->string('referensi', 30)->nullable();   // no nota / no faktur
            $t->string('catatan', 255)->nullable();    // alasan penyesuaian
            $t->unsignedSmallInteger('id_pengurus')->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->foreign('id_barang')->references('id_barang')->on('barang')->restrictOnDelete();
            $t->foreign('id_pengurus')->references('id_pengurus')->on('pengurus')->restrictOnDelete();
            $t->index(['id_barang', 'created_at']);
            $t->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
    }
};
