@extends('layouts.toko')
@section('judul', $barang->nama_barang)

@section('isi')
@php $habis = $barang->stok_barang < 1; @endphp
<div class="mb-4">
    <a href="{{ route('toko.belanja') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-navy hover:underline dark:text-kuning">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali belanja
    </a>
</div>

<div class="grid gap-8 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-xs dark:border-slate-700 dark:bg-slate-800 md:grid-cols-2">
    {{-- Image Container --}}
    <div class="group relative flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-700/60 border border-slate-200/60 dark:border-slate-700">
        <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama_barang }}"
             class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
    </div>

    {{-- Details --}}
    <div class="flex flex-col">
        <div class="flex items-center gap-2">
            <a href="{{ route('toko.belanja', ['kategori' => $barang->kategori->slug_kategori]) }}"
               class="rounded-full bg-blue-100 px-3 py-0.5 text-xs font-bold text-navy dark:bg-blue-900/50 dark:text-blue-200 hover:bg-blue-200 transition">
                {{ $barang->kategori->nama_kategori }}
            </a>
            <span class="text-xs text-slate-400">Kode: {{ $barang->kode_barang }}</span>
        </div>

        <h1 class="mt-2 text-2xl font-extrabold text-navy dark:text-white sm:text-3xl tracking-tight">
            {{ $barang->nama_barang }}
        </h1>

        <div class="mt-4 flex items-baseline gap-3">
            <p class="text-3xl font-black text-navy dark:text-kuning">
                {{ \App\Support\Rupiah::format($barang->harga_jual_barang) }}
            </p>
            <span class="text-xs text-slate-400">/ {{ $barang->satuan_barang ?? 'item' }}</span>
        </div>

        <div class="mt-3">
            @if ($habis)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                    <span class="h-2 w-2 rounded-full bg-red-600"></span> Stok habis
                </span>
            @elseif ($barang->stok_barang <= $barang->stok_min_barang)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span> Stok terbatas: sisa {{ $barang->stok_barang }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Stok tersedia: {{ $barang->stok_barang }}
                </span>
            @endif
        </div>

        @if ($barang->deskripsi_barang)
            <div class="mt-6 border-t border-slate-100 pt-4 dark:border-slate-700">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Produk</h3>
                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ $barang->deskripsi_barang }}
                </p>
            </div>
        @endif

        <div class="mt-8 border-t border-slate-100 pt-6 dark:border-slate-700">
            @if (auth()->user()?->isAnggota())
                <form method="POST" action="{{ route('toko.keranjang.tambah') }}"
                      class="flex flex-wrap items-center gap-3"
                      x-data="{ qty: 1, max: {{ max(1, $barang->stok_barang) }}, loading: false }"
                      @submit.prevent="
                          if (loading) return;
                          loading = true;
                          window.tambahKeranjang($event, {{ $barang->id_barang }}, qty, '{{ addslashes($barang->nama_barang) }}')
                              .finally(() => { loading = false; });
                      ">
                    @csrf
                    <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                    <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700">
                        <button type="button" @click="if(qty > 1) qty--" class="px-3 py-2 text-slate-500 hover:text-navy dark:text-slate-300" @disabled($habis)>-</button>
                        <input type="number" name="qty" x-model="qty" min="1" max="{{ max(1, $barang->stok_barang) }}" class="w-14 text-center border-0 p-0 text-sm font-bold text-navy dark:text-white focus:ring-0" aria-label="Jumlah" @disabled($habis)>
                        <button type="button" @click="if(qty < max) qty++" class="px-3 py-2 text-slate-500 hover:text-navy dark:text-slate-300" @disabled($habis)>+</button>
                    </div>
                    <button type="submit" @disabled($habis) class="btn-navy gap-2 px-6 py-2.5 text-xs font-bold shadow-md hover:shadow-lg transition-all active:scale-95 disabled:cursor-not-allowed disabled:opacity-50">
                        <template x-if="!loading">
                            <span class="inline-flex items-center gap-1.5">
                                <x-ikon nama="keranjang" class="h-4 w-4" /> {{ $habis ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menambahkan...
                            </span>
                        </template>
                    </button>
                </form>
            @elseif (! auth()->check())
                <a href="{{ route('login') }}" class="btn-navy font-semibold text-xs py-3 px-6 shadow-md hover:shadow-lg transition">Masuk untuk berbelanja</a>
            @else
                <p class="rounded-xl bg-slate-50 p-3.5 text-xs text-slate-500 dark:bg-slate-700/50 dark:text-slate-300">Keranjang hanya tersedia untuk akun anggota.</p>
            @endif
        </div>
    </div>
</div>

@if ($terkait->isNotEmpty())
    <div class="mt-10">
        <h2 class="text-xl font-bold text-navy dark:text-white mb-4">Produk Terkait</h2>
        <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
            @foreach ($terkait as $b)
                @include('toko._kartu', ['b' => $b])
            @endforeach
        </div>
    </div>
@endif
@endsection
