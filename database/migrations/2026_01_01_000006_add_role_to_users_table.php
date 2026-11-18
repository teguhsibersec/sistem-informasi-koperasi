<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username', 50)->nullable()->unique()->after('name');
            $t->enum('role', ['admin', 'pengurus', 'anggota'])->default('anggota')->after('password');
            $t->unsignedSmallInteger('id_pengurus')->nullable()->after('role');
            $t->unsignedInteger('id_anggota')->nullable()->after('id_pengurus');

            $t->foreign('id_pengurus')->references('id_pengurus')->on('pengurus')->restrictOnDelete();
            $t->foreign('id_anggota')->references('id_anggota')->on('anggota')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['id_pengurus']);
            $t->dropForeign(['id_anggota']);
            $t->dropColumn(['username', 'role', 'id_pengurus', 'id_anggota']);
        });
    }
};
