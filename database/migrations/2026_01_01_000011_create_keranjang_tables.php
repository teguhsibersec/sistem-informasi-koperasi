<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $t) {
            $t->increments('id_keranjang');
            $t->unsignedInteger('id_anggota')->unique();
            $t->timestamps();

            $t->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();
        });

        Schema::create('keranjang_item', function (Blueprint $t) {
            $t->increments('id_keranjang_item');
            $t->unsignedInteger('id_keranjang');
            $t->unsignedInteger('id_barang');
            $t->unsignedSmallInteger('qty');
            $t->timestamps();

            $t->foreign('id_keranjang')->references('id_keranjang')->on('keranjang')->cascadeOnDelete();
            $t->foreign('id_barang')->references('id_barang')->on('barang')->restrictOnDelete();
            $t->unique(['id_keranjang', 'id_barang']);
        });

        DB::statement('ALTER TABLE keranjang_item ADD CONSTRAINT chk_keranjang_item_qty CHECK (qty > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang_item');
        Schema::dropIfExists('keranjang');
    }
};
