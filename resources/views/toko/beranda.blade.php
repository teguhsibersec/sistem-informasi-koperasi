@extends('layouts.toko')
@section('judul', 'Beranda')

@section('isi')
{{-- 1. Hero Banner Carousel with Smooth Hardware-Accelerated Track & Dynamic Admin Logo --}}
<section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#07154a] via-navy to-[#122a85] text-white shadow-xl select-none"
         x-data="{
             currentSlide: 0,
             totalSlides: 3,
             timer: null,
             touchStartX: 0,
             touchEndX: 0,
             init() {
                 this.startAutoSlide();
             },
             startAutoSlide() {
                 this.stopAutoSlide();
                 this.timer = setInterval(() => {
                     this.nextSlide();
                 }, 5000);
             },
             stopAutoSlide() {
                 if (this.timer) {
                     clearInterval(this.timer);
                     this.timer = null;
                 }
             },
             setSlide(idx) {
                 this.currentSlide = idx;
                 this.startAutoSlide();
             },
             nextSlide() {
                 this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
             },
             prevSlide() {
                 this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
             },
             handleTouchStart(e) {
                 this.touchStartX = e.touches[0].clientX;
             },
             handleTouchEnd(e) {
                 this.touchEndX = e.changedTouches[0].clientX;
                 const diff = this.touchStartX - this.touchEndX;
                 if (Math.abs(diff) > 40) {
                     if (diff > 0) this.nextSlide();
                     else this.prevSlide();
                     this.startAutoSlide();
                 }
             }
         }"
         @mouseenter="stopAutoSlide()"
         @mouseleave="startAutoSlide()"
         @touchstart.passive="handleTouchStart($event)"
         @touchend.passive="handleTouchEnd($event)">

    {{-- Ambient Gradient Blobs --}}
    <div class="pointer-events-none absolute -left-16 -top-16 h-72 w-72 rounded-full bg-kuning/20 blur-3xl animate-blob-1"></div>
    <div class="pointer-events-none absolute -right-20 top-1/3 h-80 w-80 rounded-full bg-sky-400/20 blur-3xl animate-blob-2"></div>
    <div class="pointer-events-none absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-blue-500/15 blur-3xl animate-blob-3"></div>

    {{-- Dot Grid Texture --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.06] [background-image:radial-gradient(rgba(255,255,255,0.9)_1.25px,transparent_1.25px)] [background-size:24px_24px]"></div>

    {{-- Slides Track (Smooth Horizontal Translation without Reflow/Stutter) --}}
    <div class="relative z-10 w-full overflow-hidden">
        <div class="flex w-full transition-transform duration-700 ease-out will-change-transform"
             :style="'transform: translate3d(-' + (currentSlide * 100) + '%, 0, 0)'">
            
            {{-- Slide 1 --}}
            <div class="w-full shrink-0 min-w-full p-6 sm:p-10 md:p-14 min-h-[380px] flex items-center">
                <div class="grid w-full items-center gap-8 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-kuning backdrop-blur-xs border border-white/15">
                            <span class="h-2 w-2 rounded-full bg-kuning animate-ping"></span>
                            Koperasi Sekolah Terpadu
                        </span>
                        <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">
                            Belanja kebutuhan sekolah <span class="text-kuning">lebih mudah</span>
                        </h1>
                        <p class="mt-4 max-w-lg text-sm sm:text-base text-white/80 leading-relaxed">
                            Dari alat tulis, kebutuhan harian, hingga perlengkapan sekolah, semua ada di sini!
                        </p>
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('toko.belanja') }}" class="btn-kuning gap-2 px-6 py-3 font-bold shadow-lg transition-transform hover:scale-105">
                                <x-ikon nama="keranjang" class="h-5 w-5" /> Mulai Belanja
                            </a>
                            <a href="{{ route('toko.tentang') }}" class="btn border border-white/20 bg-white/10 px-5 py-3 text-xs sm:text-sm font-semibold text-white backdrop-blur-xs hover:bg-white/20 transition">
                                Tentang Kami
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:col-span-5 md:flex md:justify-center relative">
                        {{-- Floating Graphic Group with Logo following Admin Upload --}}
                        @php $logoKoperasi = \App\Models\Pengaturan::ambil('logo_koperasi') @endphp
                        <div class="relative h-72 w-72 lg:h-80 lg:w-80 flex items-center justify-center">
                            {{-- Concentric Orbit Rings --}}
                            <div class="absolute inset-0 rounded-full border border-white/10"></div>
                            <div class="absolute inset-6 rounded-full border border-white/15"></div>
                            <div class="absolute inset-12 rounded-full border border-kuning/25"></div>
                            
                            {{-- Center School Logo Card --}}
                            <div class="relative z-10 flex h-32 w-32 items-center justify-center rounded-3xl bg-white/15 p-4 shadow-2xl backdrop-blur-md border border-white/25 animate-float-1">
                                @if ($logoKoperasi)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-20 w-20 rounded-2xl bg-white object-contain p-2 shadow-xl">
                                @else
                                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-kuning text-navy shadow-lg font-bold text-2xl">UM</span>
                                @endif
                            </div>

                            {{-- Floating Icon Badges Sesuai Isi Web --}}
                            {{-- 1. Top Center: Seragam & Atribut --}}
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md border border-white/20 shadow-lg whitespace-nowrap animate-float-1 hover:bg-white/25 transition">
                                <span class="text-kuning text-sm">👕</span> Seragam & Atribut
                            </div>

                            {{-- 2. Top Right: ATK Lengkap --}}
                            <div class="absolute top-12 -right-4 lg:-right-8 z-20 flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md border border-white/20 shadow-lg whitespace-nowrap animate-float-2 hover:bg-white/25 transition">
                                <span class="text-kuning text-sm">📚</span> ATK Lengkap
                            </div>

                            {{-- 3. Bottom Right: Bayar via QRIS --}}
                            <div class="absolute bottom-10 -right-2 lg:-right-6 z-20 flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md border border-white/20 shadow-lg whitespace-nowrap animate-float-4 hover:bg-white/25 transition">
                                <span class="text-kuning text-sm">💳</span> Bayar via QRIS
                            </div>

                            {{-- 4. Bottom Left: Siap Ambil --}}
                            <div class="absolute bottom-10 -left-2 lg:-left-6 z-20 flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md border border-white/20 shadow-lg whitespace-nowrap animate-float-3 hover:bg-white/25 transition">
                                <span class="text-kuning text-sm">⚡</span> Siap Ambil
                            </div>

                            {{-- 5. Top Left: Snack & Minuman --}}
                            <div class="absolute top-12 -left-4 lg:-left-8 z-20 flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md border border-white/20 shadow-lg whitespace-nowrap animate-float-5 hover:bg-white/25 transition">
                                <span class="text-kuning text-sm">🥤</span> Snack & Minuman
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div class="w-full shrink-0 min-w-full p-6 sm:p-10 md:p-14 min-h-[380px] flex items-center">
                <div class="grid w-full items-center gap-8 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-3 py-1 text-xs font-semibold text-kuning backdrop-blur-xs border border-amber-300/30">
                            Promo Spesial Anggota
                        </span>
                        <h2 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">
                            Harga Lebih Hemat, <span class="text-kuning">Bebas Antre</span>
                        </h2>
                        <p class="mt-4 max-w-lg text-sm sm:text-base text-white/80 leading-relaxed">
                            Khusus siswa anggota SMP Negeri 2 Metro. Pesan langsung dari smartphone dan nikmati diskon khusus anggota.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('toko.belanja') }}" class="btn-kuning gap-2 px-6 py-3 font-bold shadow-lg transition-transform hover:scale-105">
                                Lihat Katalog Promo →
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:col-span-5 md:flex md:justify-center">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-md shadow-2xl text-center max-w-xs animate-card-1">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-400/20 text-kuning mb-3">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-lg font-bold text-white">Diskon Hingga 15%</p>
                            <p class="mt-1 text-xs text-white/70">Untuk pembelian paket alat tulis dan seragam sekolah</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div class="w-full shrink-0 min-w-full p-6 sm:p-10 md:p-14 min-h-[380px] flex items-center">
                <div class="grid w-full items-center gap-8 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-300 backdrop-blur-xs border border-emerald-300/30">
                            Pembayaran Cepat & Aman
                        </span>
                        <h2 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">
                            Bayar via QRIS, <span class="text-kuning">Praktis Tanpa Tunai</span>
                        </h2>
                        <p class="mt-4 max-w-lg text-sm sm:text-base text-white/80 leading-relaxed">
                            Mendukung pembayaran digital QRIS dinamis langsung dari rekening atau e-wallet orang tua maupun siswa.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('toko.belanja') }}" class="btn-kuning gap-2 px-6 py-3 font-bold shadow-lg transition-transform hover:scale-105">
                                Mulai Belanja Sekarang
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:col-span-5 md:flex md:justify-center">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-md shadow-2xl text-center max-w-xs animate-card-2">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-400/20 text-emerald-300 mb-3">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <p class="text-lg font-bold text-white">QRIS Dinamis Otomatis</p>
                            <p class="mt-1 text-xs text-white/70">Nominal pas tanpa perlu konfirmasi manual</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Carousel Controls: Dots & Navigation Arrows --}}
    <div class="relative z-10 flex items-center justify-between border-t border-white/10 px-6 py-3 sm:px-10">
        <div class="flex items-center gap-2">
            <template x-for="i in totalSlides" :key="i">
                <button type="button" @click="setSlide(i - 1)"
                        class="h-2 rounded-full transition-all duration-300"
                        :class="currentSlide === (i - 1) ? 'w-8 bg-kuning' : 'w-2 bg-white/40 hover:bg-white/70'"
                        :aria-label="'Slide ' + i"></button>
            </template>
        </div>
        <div class="flex items-center gap-1.5">
            <button type="button" @click="prevSlide()"
                    class="rounded-full p-1.5 text-white/70 hover:bg-white/15 hover:text-white transition"
                    aria-label="Slide sebelumnya">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button type="button" @click="nextSlide()"
                    class="rounded-full p-1.5 text-white/70 hover:bg-white/15 hover:text-white transition"
                    aria-label="Slide selanjutnya">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</section>

{{-- Animated Counter Stats Bar --}}
<section class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
    @php
        $stats = [
            ['1.000+', 'Produk Pilihan', 'Alat tulis & seragam lengkap', 'text-blue-600 dark:text-blue-400'],
            ['500+', 'Siswa Anggota', 'Terdaftar aktif di koperasi', 'text-amber-500 dark:text-kuning'],
            ['15 Menit', 'Rata-rata Siap Ambil', 'Pesan dari kelas langsung jadi', 'text-emerald-600 dark:text-emerald-400'],
            ['100%', 'Transparan & Aman', 'Laporan & kas tercatat rapi', 'text-indigo-600 dark:text-indigo-400'],
        ];
    @endphp
    @foreach ($stats as [$angka, $judul, $sub, $warna])
        <div class="flex flex-col justify-center rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-700/80 dark:bg-slate-800">
            <span class="text-xl sm:text-2xl font-black {{ $warna }}">{{ $angka }}</span>
            <span class="mt-1 text-xs font-bold text-slate-800 dark:text-slate-100">{{ $judul }}</span>
            <span class="text-[11px] text-slate-400 dark:text-slate-400">{{ $sub }}</span>
        </div>
    @endforeach
</section>

{{-- 2. Kategori dengan Warna Unik & Efek Goyang / Hover Lift --}}
<section class="mt-8">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-xl font-bold text-navy dark:text-white">Kategori Pilihan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Temukan barang kebutuhan sekolah berdasarkan kelompoknya</p>
        </div>
        <a href="{{ route('toko.belanja') }}" class="text-xs font-semibold text-navy hover:text-blue-700 dark:text-kuning hover:underline">Lihat Semua →</a>
    </div>

    @php
        $paletKategori = [
            'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
            'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300',
            'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
            'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300',
        ];
    @endphp

    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6">
        <a href="{{ route('toko.belanja') }}"
           class="group flex flex-col items-center gap-2 rounded-2xl border border-slate-200/80 bg-white p-4 text-center text-xs font-semibold text-slate-700 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-navy/40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            <span class="flex h-13 w-13 items-center justify-center rounded-2xl bg-slate-100 text-navy transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6 dark:bg-slate-700 dark:text-kuning shadow-inner">
                <x-ikon nama="grid" class="h-6 w-6" />
            </span>
            <span class="truncate w-full group-hover:text-navy dark:group-hover:text-kuning">Semua Produk</span>
        </a>

        @foreach ($kategori as $idx => $k)
            @php $warna = $paletKategori[$idx % count($paletKategori)]; @endphp
            <a href="{{ route('toko.belanja', ['kategori' => $k->slug_kategori]) }}"
               class="group flex flex-col items-center gap-2 rounded-2xl border border-slate-200/80 bg-white p-4 text-center text-xs font-semibold text-slate-700 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-navy/40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                <span class="flex h-13 w-13 items-center justify-center rounded-2xl {{ $warna }} transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6 shadow-inner">
                    <x-ikon :nama="$k->ikon ?? 'grid'" class="h-6 w-6" />
                </span>
                <span class="truncate w-full group-hover:text-navy dark:group-hover:text-kuning">{{ $k->nama_kategori }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- 3. Section Promo Flash Sale dengan Live Countdown Timer --}}
<section class="mt-8 rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 p-6 text-white shadow-lg"
         x-data="{
             hours: 4,
             minutes: 32,
             seconds: 45,
             init() {
                 setInterval(() => {
                     if (this.seconds > 0) {
                         this.seconds--;
                     } else {
                         this.seconds = 59;
                         if (this.minutes > 0) {
                             this.minutes--;
                         } else {
                             this.minutes = 59;
                             if (this.hours > 0) this.hours--;
                         }
                     }
                 }, 1000);
             }
         }">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">Flash Sale</span>
                <span class="text-xs font-semibold text-white/90">Berakhir dalam:</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/40 text-base font-black" x-text="String(hours).padStart(2, '0')">04</div>
                <span class="font-bold">:</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/40 text-base font-black" x-text="String(minutes).padStart(2, '0')">32</div>
                <span class="font-bold">:</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/40 text-base font-black" x-text="String(seconds).padStart(2, '0')">45</div>
            </div>
        </div>
        <a href="{{ route('toko.belanja') }}" class="rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-orange-600 shadow-md transition hover:bg-orange-50">
            Lihat Semua Promo →
        </a>
    </div>
</section>

{{-- 4. Section Produk Terbaru (Grid) --}}
<section class="mt-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-navy dark:text-white">Produk Terbaru</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Barang perlengkapan yang baru saja tiba di etalase koperasi</p>
        </div>
        <a href="{{ route('toko.belanja') }}" class="text-xs font-semibold text-navy hover:text-blue-700 dark:text-kuning hover:underline">Lihat Semua →</a>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
        @forelse ($terbaru as $b)
            @include('toko._kartu', ['b' => $b])
        @empty
            <p class="col-span-full py-12 text-center text-slate-400">Belum ada produk.</p>
        @endforelse
    </div>
</section>

{{-- 3b. Section Paket Hemat Sekolah --}}
<section class="mt-10">
    <div class="mb-4">
        <h2 class="text-xl font-bold text-navy dark:text-white">Paket Hemat Siswa</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Kombinasi produk praktis dengan harga lebih terjangkau</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Paket Ujian</span>
            <h3 class="mt-3 text-base font-bold text-navy dark:text-white">Paket Alat Tulis Lengkap</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pensil 2B, Penghapus khusus ujian, Penggaris segitiga, dan Papan alas ujian.</p>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                <span class="text-base font-bold text-navy dark:text-kuning">Rp 18.500</span>
                <a href="{{ route('toko.belanja') }}" class="btn-navy py-1.5 px-3 text-xs">Pesan Paket</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Paket Seragam</span>
            <h3 class="mt-3 text-base font-bold text-navy dark:text-white">Atribut Sekolah Standar</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Dasi biru SMP, Topi sekolah bordir resmi, Sabuk gesper logo, dan Kaos kaki putih.</p>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                <span class="text-base font-bold text-navy dark:text-kuning">Rp 35.000</span>
                <a href="{{ route('toko.belanja') }}" class="btn-navy py-1.5 px-3 text-xs">Pesan Paket</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Paket Istirahat</span>
            <h3 class="mt-3 text-base font-bold text-navy dark:text-white">Kombinasi Snack Sehat</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Roti gandum segar, Susu UHT kotak 200ml, dan Air mineral kemasan botol higienis.</p>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                <span class="text-base font-bold text-navy dark:text-kuning">Rp 12.000</span>
                <a href="{{ route('toko.belanja') }}" class="btn-navy py-1.5 px-3 text-xs">Pesan Paket</a>
            </div>
        </div>
    </div>
</section>

{{-- 5. Pengumuman Koperasi & Testimoni Anggota --}}
<section class="mt-10 grid gap-6 md:grid-cols-12">
    {{-- Pengumuman Koperasi --}}
    <div class="md:col-span-7 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-navy dark:bg-blue-900/40 dark:text-kuning">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </span>
            <h3 class="text-lg font-bold text-navy dark:text-white">Pengumuman Koperasi</h3>
        </div>
        <div class="space-y-3.5">
            <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 dark:border-slate-700/60 dark:bg-slate-800/80">
                <span class="rounded bg-navy/10 px-2 py-0.5 text-[10px] font-bold text-navy dark:bg-blue-900/50 dark:text-blue-200">Info Layanan</span>
                <h4 class="mt-1 text-sm font-bold text-slate-800 dark:text-white">Jadwal Operasional Selama Ujian Semester</h4>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Koperasi buka lebih awal pukul 06.45 WIB untuk melayani pembelian alat tulis darurat bagi siswa.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 dark:border-slate-700/60 dark:bg-slate-800/80">
                <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">Sistem Baru</span>
                <h4 class="mt-1 text-sm font-bold text-slate-800 dark:text-white">Pembayaran Digital QRIS Otomatis</h4>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sekarang pembayaran belanja online di koperasi bisa langsung dipindai tanpa perlu antre di loket kasir.</p>
            </div>
        </div>
    </div>

    {{-- Testimoni Anggota --}}
    <div class="md:col-span-5 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-kuning">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </span>
            <h3 class="text-lg font-bold text-navy dark:text-white">Kata Anggota</h3>
        </div>
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-100 p-3.5 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="flex items-center gap-1 text-amber-400 text-xs">★★★★★</div>
                <p class="mt-2 text-xs italic text-slate-600 dark:text-slate-300">"Sangat membantu! Bisa pesan seragam dan buku dari rumah, besoknya tinggal ambil di koperasi tanpa desak-desakan."</p>
                <p class="mt-2 text-[11px] font-bold text-navy dark:text-kuning">— Muhammad Rizky, Siswa Kelas VIII-B</p>
            </div>
            <div class="rounded-xl border border-slate-100 p-3.5 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="flex items-center gap-1 text-amber-400 text-xs">★★★★★</div>
                <p class="mt-2 text-xs italic text-slate-600 dark:text-slate-300">"Harga barang sangat terjangkau dibanding di luar sekolah, kualitas alat tulisnya juga bagus dan original."</p>
                <p class="mt-2 text-[11px] font-bold text-navy dark:text-kuning">— Siti Aisyah, Siswi Kelas VII-A</p>
            </div>
        </div>
    </div>
</section>

{{-- Keunggulan Koperasi --}}
<section class="mt-10 grid gap-4 rounded-3xl bg-blue-50/70 p-6 sm:grid-cols-2 lg:grid-cols-4 dark:bg-slate-800/60 border border-blue-100 dark:border-slate-700">
    @foreach ([
        ['truk', 'Pengambilan Cepat', 'Pesanan langsung diproses pengurus'],
        ['perisai', 'Produk Original', 'Kualitas terjamin untuk siswa'],
        ['headset', 'Layanan Responsif', 'Buka ' . str_replace('-', ' - ', $jamLayanan) . ' WIB'],
        ['komunitas', 'Dukung Koperasi', 'Dari siswa, oleh pengurus, untuk kemajuan bersama'],
    ] as [$ikon, $judul, $ket])
        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-navy shadow-sm dark:bg-slate-700 dark:text-kuning">
                <x-ikon :nama="$ikon" class="h-6 w-6" />
            </span>
            <div class="leading-tight">
                <p class="text-sm font-bold text-navy dark:text-white">{{ $judul }}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $ket }}</p>
            </div>
        </div>
    @endforeach
</section>
@endsection
