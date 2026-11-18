<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $t) {
            $t->smallIncrements('id_kategori');
            $t->string('nama_kategori', 50)->unique();
            $t->string('slug_kategori', 60)->unique();
            $t->string('ikon', 50)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
