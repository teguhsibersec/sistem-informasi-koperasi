<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $t) {
            $t->increments('id_pembelian');
            $t->string('no_faktur_pembelian', 20);
            $t->date('tgl_pembelian');
            $t->unsignedInteger('id_pemasok');
            $t->enum('status_pembelian', ['dipesan', 'diterima'])->default('dipesan');
            $t->timestamps();

            $t->foreign('id_pemasok')->references('id_pemasok')->on('pemasok')->restrictOnDelete();
            $t->index('tgl_pembelian');
            $t->index('status_pembelian');
        });

        Schema::create('detail_pembelian', function (Blueprint $t) {
            $t->unsignedInteger('id_pembelian');
            $t->unsignedInteger('id_barang');
            $t->integer('qty_detail_pembelian');
            $t->decimal('harga_beli_detail_pembelian', 12, 2);

            $t->primary(['id_pembelian', 'id_barang']);
            $t->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->restrictOnDelete();
            $t->foreign('id_barang')->references('id_barang')->on('barang')->restrictOnDelete();
            $t->index('id_barang');
        });

        DB::statement('ALTER TABLE detail_pembelian ADD CONSTRAINT chk_detail_pembelian_qty CHECK (qty_detail_pembelian > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
        Schema::dropIfExists('pembelian');
    }
};
