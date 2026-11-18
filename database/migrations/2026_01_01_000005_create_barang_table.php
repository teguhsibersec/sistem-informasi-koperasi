<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $t) {
            $t->increments('id_barang');
            $t->string('kode_barang', 10)->unique();
            $t->string('nama_barang', 100);
            $t->unsignedSmallInteger('id_kategori'); // Jenis barang
            $t->string('satuan_barang', 25)->default('pcs');
            $t->decimal('harga_jual_barang', 12, 2);
            $t->integer('stok_barang')->default(0);
            $t->integer('stok_min_barang')->default(5);
            $t->string('gambar_barang', 255)->nullable();
            $t->text('deskripsi_barang')->nullable();
            $t->enum('status_barang', ['aktif', 'nonaktif'])->default('aktif');
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('id_kategori')->references('id_kategori')->on('kategori')->restrictOnDelete();
            $t->index('status_barang');
        });

        DB::statement('ALTER TABLE barang ADD CONSTRAINT chk_barang_harga CHECK (harga_jual_barang >= 0)');
        DB::statement('ALTER TABLE barang ADD CONSTRAINT chk_barang_stok CHECK (stok_barang >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
