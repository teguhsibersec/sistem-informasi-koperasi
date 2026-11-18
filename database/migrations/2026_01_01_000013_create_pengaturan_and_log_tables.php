<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $t) {
            $t->string('kunci', 64)->primary();
            $t->text('nilai')->nullable();
            $t->timestamps();
        });

        Schema::create('log_aktivitas', function (Blueprint $t) {
            $t->bigIncrements('id_log');
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('aksi', 30);          // tambah | ubah | hapus | batal | dll
            $t->string('tabel', 50);
            $t->string('id_rekaman', 30)->nullable();
            $t->json('perubahan')->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $t->index(['tabel', 'id_rekaman']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('pengaturan');
    }
};
