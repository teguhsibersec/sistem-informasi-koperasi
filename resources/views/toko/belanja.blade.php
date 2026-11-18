@extends('layouts.toko')
@section('judul', 'Katalog Belanja')

@section('isi')
<div x-data="{
        mobileFilter: false,
        viewMode: localStorage.getItem('belanja_view') || 'grid',
        setView(m) {
            this.viewMode = m;
            localStorage.setItem('belanja_view', m);
        }
     }">

    {{-- Header Banner & Breadcrumbs --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-gradient-to-r from-navy via-navy-700 to-blue-800 p-6 text-white shadow-md">
        <div>
            <h1 class="text-2xl font-extrabold sm:text-3xl tracking-tight">Katalog Belanja Koperasi</h1>
            <p class="mt-1 text-xs sm:text-sm text-white/80">Temukan perlengkapan sekolah original, harga terjangkau, dan berkualitas.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="mobileFilter = true"
                    class="flex items-center gap-2 rounded-xl bg-white/10 px-3.5 py-2 text-xs font-semibold backdrop-blur-xs border border-white/20 hover:bg-white/20 transition lg:hidden">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter Produk
            </button>
            {{-- View Mode Toggle --}}
            <div class="hidden items-center rounded-xl bg-white/10 p-1 border border-white/15 backdrop-blur-xs sm:flex">
                <button type="button" @click="setView('grid')"
                        class="rounded-lg p-1.5 transition"
                        :class="viewMode === 'grid' ? 'bg-kuning text-navy shadow-xs' : 'text-white/70 hover:text-white'"
                        title="Tampilan Grid">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </button>
                <button type="button" @click="setView('list')"
                        class="rounded-lg p-1.5 transition"
                        :class="viewMode === 'list' ? 'bg-kuning text-navy shadow-xs' : 'text-white/70 hover:text-white'"
                        title="Tampilan List">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Active Filter Chips --}}
    @if ($q !== '' || $kategoriAktif)
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Filter Aktif:</span>
            @if ($kategoriAktif)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-navy dark:bg-blue-900/50 dark:text-blue-200">
                    Kategori: {{ $kategoriAktif->nama_kategori }}
                    <a href="{{ route('toko.belanja', array_filter(['q' => $q])) }}" class="hover:text-red-600">×</a>
                </span>
            @endif
            @if ($q !== '')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900 dark:bg-amber-950/60 dark:text-amber-200">
                    Cari: “{{ $q }}”
                    <a href="{{ route('toko.belanja', array_filter(['kategori' => $kategoriAktif?->slug_kategori])) }}" class="hover:text-red-600">×</a>
                </span>
            @endif
            <a href="{{ route('toko.belanja') }}" class="text-xs font-semibold text-red-500 hover:underline ml-1">Reset Semua</a>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-4">
        {{-- Desktop Sidebar Filter --}}
        <aside class="hidden lg:block space-y-5">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-navy dark:text-white">Kategori</h3>
                    <a href="{{ route('toko.belanja', array_filter(['q' => $q])) }}" class="text-[11px] font-semibold text-slate-400 hover:text-navy dark:hover:text-kuning">Semua</a>
                </div>
                <ul class="mt-3 space-y-1.5">
                    <li>
                        <a href="{{ route('toko.belanja', array_filter(['q' => $q])) }}"
                           class="flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition {{ ! $kategoriAktif ? 'bg-navy text-white shadow-xs dark:bg-blue-600' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/60' }}">
                            <span>Semua Kategori</span>
                        </a>
                    </li>
                    @foreach ($kategori as $k)
                        @php $aktif = $kategoriAktif?->id_kategori === $k->id_kategori; @endphp
                        <li>
                            <a href="{{ route('toko.belanja', array_filter(['kategori' => $k->slug_kategori, 'q' => $q])) }}"
                               class="flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition {{ $aktif ? 'bg-navy text-white shadow-xs dark:bg-blue-600' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/60' }}">
                                <span>{{ $k->nama_kategori }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Info Card --}}
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                <h4 class="text-xs font-bold text-navy dark:text-kuning">Belanja Praktis</h4>
                <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    Pesanan online disiapkan pengurus dalam waktu 15 menit. Anda dapat mengambil pesanan di koperasi sekolah saat jam istirahat.
                </p>
            </div>
        </aside>

        {{-- Mobile Filter Drawer --}}
        <div x-show="mobileFilter" x-cloak class="fixed inset-0 z-50 flex lg:hidden">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="mobileFilter = false"></div>
            <div class="relative ml-auto w-full max-w-xs bg-white p-6 shadow-xl dark:bg-slate-800 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b pb-3 dark:border-slate-700">
                        <h3 class="text-base font-bold text-navy dark:text-white">Pilih Kategori</h3>
                        <button type="button" @click="mobileFilter = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">✕</button>
                    </div>
                    <ul class="mt-4 space-y-1.5">
                        <li>
                            <a href="{{ route('toko.belanja', array_filter(['q' => $q])) }}"
                               class="block rounded-xl px-3 py-2 text-xs font-semibold {{ ! $kategoriAktif ? 'bg-navy text-white' : 'text-slate-600 dark:text-slate-300' }}">
                                Semua Kategori
                            </a>
                        </li>
                        @foreach ($kategori as $k)
                            <li>
                                <a href="{{ route('toko.belanja', array_filter(['kategori' => $k->slug_kategori, 'q' => $q])) }}"
                                   class="block rounded-xl px-3 py-2 text-xs font-semibold {{ $kategoriAktif?->id_kategori === $k->id_kategori ? 'bg-navy text-white' : 'text-slate-600 dark:text-slate-300' }}">
                                    {{ $k->nama_kategori }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" @click="mobileFilter = false" class="btn-navy w-full text-xs">Terapkan Filter</button>
            </div>
        </div>

        {{-- Product Results Area --}}
        <div class="lg:col-span-3">
            <div id="hasil-cari">
                @include('toko._produk', ['produk' => $produk, 'q' => $q])
            </div>
        </div>
    </div>
</div>
@endsection
