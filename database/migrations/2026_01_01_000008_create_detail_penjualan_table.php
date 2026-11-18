<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan', function (Blueprint $t) {
            $t->unsignedInteger('id_penjualan');
            $t->unsignedInteger('id_barang');
            $t->unsignedSmallInteger('qty_detail_penjualan');
            $t->decimal('harga_satuan_detail_penjualan', 12, 2); // snapshot harga

            $t->primary(['id_penjualan', 'id_barang']);
            $t->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->restrictOnDelete();
            $t->foreign('id_barang')->references('id_barang')->on('barang')->restrictOnDelete();
            $t->index('id_barang');
        });

        DB::statement('ALTER TABLE detail_penjualan ADD CONSTRAINT chk_detail_penjualan_qty CHECK (qty_detail_penjualan > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
