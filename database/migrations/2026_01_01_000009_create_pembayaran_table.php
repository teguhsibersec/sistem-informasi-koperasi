<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $t) {
            $t->increments('id_pembayaran');
            $t->unsignedInteger('id_penjualan');
            $t->enum('metode_pembayaran', ['cash', 'qris']);
            $t->decimal('jumlah_tagihan', 12, 2);
            $t->decimal('uang_diterima', 12, 2)->nullable();
            $t->decimal('kembalian', 12, 2)->nullable();
            $t->enum('status_pembayaran', ['menunggu', 'berhasil', 'gagal', 'kedaluwarsa'])->default('menunggu');
            $t->string('kode_referensi', 64)->nullable();
            $t->text('qris_payload')->nullable();
            $t->string('bukti_bayar', 255)->nullable();
            $t->dateTime('dibayar_pada')->nullable();
            $t->dateTime('kedaluwarsa_pada')->nullable();
            $t->unsignedSmallInteger('dikonfirmasi_oleh')->nullable();
            $t->timestamps();

            $t->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->restrictOnDelete();
            $t->foreign('dikonfirmasi_oleh')->references('id_pengurus')->on('pengurus')->restrictOnDelete();
            $t->index('kode_referensi');
            $t->index(['status_pembayaran', 'kedaluwarsa_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
