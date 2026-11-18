<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengurus', function (Blueprint $t) {
            $t->smallIncrements('id_pengurus');
            $t->char('kode_pengurus', 3)->unique();
            $t->string('nama_pengurus', 100);
            $t->enum('jabatan_pengurus', ['ketua', 'sekretaris', 'bendahara']);
            $t->string('alamat_pengurus', 100)->nullable();
            $t->string('kota_pengurus', 40)->nullable();
            $t->string('telepon_pengurus', 15)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
