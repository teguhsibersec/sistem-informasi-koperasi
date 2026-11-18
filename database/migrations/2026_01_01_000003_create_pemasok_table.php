<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasok', function (Blueprint $t) {
            $t->increments('id_pemasok');
            $t->string('nama_pemasok', 100);
            $t->string('telepon_pemasok', 15)->nullable();
            $t->string('alamat_pemasok', 200)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok');
    }
};
