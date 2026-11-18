<?php

/** Identitas koperasi. Ubah lewat .env (KOPERASI_*) tanpa menyentuh kode. */
return [
    'nama' => env('KOPERASI_NAMA', 'KOPERASI SEKOLAH SMP NEGERI 2 METRO'),
    'subjudul' => env('KOPERASI_SUBJUDUL', 'Aplikasi Pengolahan Data Koperasi Sekolah SMP Negeri 2 Metro'),
    'alamat' => env('KOPERASI_ALAMAT', 'Metro, Lampung'),
];
