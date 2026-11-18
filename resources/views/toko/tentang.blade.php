@extends('layouts.toko')
@section('judul', 'Tentang Koperasi')

@section('isi')
{{-- 1. Hero Kecil dengan Gradien & Pola --}}
<section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#07154a] via-navy to-[#122a85] p-8 sm:p-12 text-white shadow-xl">
    <div class="pointer-events-none absolute -left-12 -top-12 h-64 w-64 rounded-full bg-kuning/20 blur-3xl animate-blob-1"></div>
    <div class="pointer-events-none absolute -right-16 top-1/2 h-72 w-72 rounded-full bg-sky-400/20 blur-3xl animate-blob-2"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.06] [background-image:radial-gradient(rgba(255,255,255,0.9)_1.25px,transparent_1.25px)] [background-size:24px_24px]"></div>

    <div class="relative z-10 max-w-2xl">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-kuning backdrop-blur-xs border border-white/15">
            Profil Lembaga
        </span>
        <h1 class="mt-4 text-3xl font-extrabold sm:text-4xl tracking-tight">Tentang {{ $nama }}</h1>
        <p class="mt-3 text-sm sm:text-base text-white/80 leading-relaxed">
            Koperasi Sekolah SMP Negeri 2 Metro yang berdedikasi menyediakan kebutuhan belajar siswa:
            alat tulis, makanan & minuman sehat, seragam sekolah, hingga atribut resmi dalam satu ekosistem terpadu.
        </p>
    </div>
</section>

{{-- 2. Statistik Animasi --}}
<section class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <p class="text-2xl font-black text-navy dark:text-kuning">1985</p>
        <p class="mt-1 text-xs font-bold text-slate-700 dark:text-slate-200">Tahun Berdiri</p>
        <p class="text-[11px] text-slate-400">Melayani lebih dari 40 tahun</p>
    </div>
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <p class="text-2xl font-black text-blue-600 dark:text-blue-400">1.000+</p>
        <p class="mt-1 text-xs font-bold text-slate-700 dark:text-slate-200">Item Produk</p>
        <p class="text-[11px] text-slate-400">ATK, buku, dan seragam</p>
    </div>
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <p class="text-2xl font-black text-amber-500 dark:text-amber-400">500+</p>
        <p class="mt-1 text-xs font-bold text-slate-700 dark:text-slate-200">Siswa Anggota</p>
        <p class="text-[11px] text-slate-400">Aktif berbelanja & menabung</p>
    </div>
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">100%</p>
        <p class="mt-1 text-xs font-bold text-slate-700 dark:text-slate-200">Transparansi</p>
        <p class="text-[11px] text-slate-400">Laporan kas tercatat rapi</p>
    </div>
</section>

{{-- 3. Visi & Misi dalam Kartu Berikon --}}
<section class="mt-8 grid gap-6 md:grid-cols-2">
    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-navy dark:bg-blue-900/40 dark:text-kuning shadow-inner">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </span>
            <h2 class="text-lg font-bold text-navy dark:text-white">Visi Koperasi</h2>
        </div>
        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            Menjadi koperasi sekolah unggulan yang mandiri, berkarakter gotong royong, dan adaptif terhadap teknologi digital dalam mendukung keberhasilan pendidikan seluruh siswa SMP Negeri 2 Metro.
        </p>
    </div>

    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-kuning shadow-inner">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <h2 class="text-lg font-bold text-navy dark:text-white">Misi Koperasi</h2>
        </div>
        <ul class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
            <li class="flex items-start gap-2">
                <span class="text-emerald-500 font-bold">✓</span> Menyediakan barang kebutuhan sekolah yang lengkap, bermutu tinggi, dan terjangkau.
            </li>
            <li class="flex items-start gap-2">
                <span class="text-emerald-500 font-bold">✓</span> Melatih literasi keuangan dan kewirausahaan bagi siswa anggota koperasi.
            </li>
            <li class="flex items-start gap-2">
                <span class="text-emerald-500 font-bold">✓</span> Menghadirkan layanan digital berbasis web dan QRIS untuk kemudahan transaksi.
            </li>
        </ul>
    </div>
</section>

{{-- 4. Struktur Pengurus Koperasi --}}
<section class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-xs dark:border-slate-700 dark:bg-slate-800">
    <div class="text-center max-w-md mx-auto mb-6">
        <h2 class="text-xl font-bold text-navy dark:text-white">Struktur Pengurus</h2>
        <p class="mt-1 text-xs text-slate-400">Pengelola amanah Koperasi Sekolah SMP Negeri 2 Metro</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-5 text-center dark:border-slate-700 dark:bg-slate-700/50">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-navy text-kuning font-bold text-lg shadow-sm">
                KT
            </div>
            <h3 class="mt-3 text-sm font-bold text-navy dark:text-white">Ketua Koperasi</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Penanggung Jawab & Kebijakan Umum</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-5 text-center dark:border-slate-700 dark:bg-slate-700/50">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white font-bold text-lg shadow-sm">
                SK
            </div>
            <h3 class="mt-3 text-sm font-bold text-navy dark:text-white">Sekretaris</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pengadaan, Inventaris & Gudang</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-5 text-center dark:border-slate-700 dark:bg-slate-700/50">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 text-white font-bold text-lg shadow-sm">
                BD
            </div>
            <h3 class="mt-3 text-sm font-bold text-navy dark:text-white">Bendahara</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Kasir, Keuangan & Pembayaran</p>
        </div>
    </div>
</section>

{{-- 5. Timeline Sejarah Singkat --}}
<section class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-xs dark:border-slate-700 dark:bg-slate-800">
    <h2 class="text-lg font-bold text-navy dark:text-white mb-6">Perjalanan Koperasi</h2>

    <div class="relative pl-6 space-y-6 border-l-2 border-blue-200 dark:border-slate-700">
        <div class="relative">
            <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-blue-600 dark:border-slate-800"></div>
            <p class="text-xs font-bold text-navy dark:text-kuning">Tahun 1985</p>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Pendirian Koperasi Siswa</h3>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Dimulai dari loket kecil penyedia buku tulis dan pensil bagi siswa.</p>
        </div>

        <div class="relative">
            <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-amber-500 dark:border-slate-800"></div>
            <p class="text-xs font-bold text-navy dark:text-kuning">Tahun 2010</p>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Pengembangan Toko Fisik Mandiri</h3>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Penyediaan etalase seragam resmi, atribut bordir, dan aneka perlengkapan ekstrakurikuler.</p>
        </div>

        <div class="relative">
            <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-800"></div>
            <p class="text-xs font-bold text-navy dark:text-kuning">Tahun 2026</p>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Transformasi Digital & POS Modern</h3>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Implementasi sistem belanja online terpadu, kasir POS, dan pembayaran QRIS otomatis.</p>
        </div>
    </div>
</section>

{{-- 6. Info Lokasi, Jam Layanan & WhatsApp --}}
<section class="mt-8 grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 text-navy dark:text-kuning mb-2">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat Lokasi</h3>
        </div>
        <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-white leading-relaxed">{{ $alamat }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 text-navy dark:text-kuning mb-2">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Jam Layanan</h3>
        </div>
        <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-white">{{ str_replace('-', ' - ', $jamLayanan) }} WIB</p>
        <p class="text-[11px] text-slate-400 mt-1">Buka setiap hari sekolah (Senin s/d Jumat)</p>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 mb-2">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.662 1.435 5.183L2 22l4.954-1.399C8.423 21.493 10.15 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Bantuan WhatsApp</h3>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Punya pertanyaan seputar produk atau pesanan?</p>
        <a href="{{ \App\Models\Pengaturan::noAdminWaLink('Halo Admin Koperasi Sekolah SMP Negeri 2 Metro') }}" target="_blank" rel="noopener noreferrer" class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full text-xs font-bold gap-2">
            Chat WhatsApp Koperasi
        </a>
    </div>
</section>
@endsection
