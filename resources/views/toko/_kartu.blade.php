@php
    $habis = $b->stok_barang < 1;
    $isBaru = $b->created_at && $b->created_at->diffInDays(now()) <= 30;
@endphp
<div class="group relative flex flex-col rounded-2xl border border-slate-200/80 bg-white p-3 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-slate-700/80 dark:bg-slate-800"
     x-data="{ wishlist: false, quickView: false }">

    {{-- Wishlist Button --}}
    <button type="button"
            @click.stop.prevent="wishlist = !wishlist; window.tampilkanToast(wishlist ? 'Disimpan ke daftar favorit!' : 'Dihapus dari favorit', 'sukses')"
            class="absolute right-5 top-5 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur-xs transition-all duration-200 hover:scale-110 dark:bg-slate-800/90"
            :class="wishlist ? 'text-rose-500' : 'text-slate-400 hover:text-rose-500 dark:text-slate-300'"
            aria-label="Simpan ke favorit">
        <svg class="h-4 w-4 transition-colors" :fill="wishlist ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>

    {{-- Badges --}}
    <div class="absolute left-5 top-5 z-20 flex flex-col gap-1 pointer-events-none">
        @if ($habis)
            <span class="rounded-full bg-slate-800/90 px-2 py-0.5 text-[10px] font-bold text-white shadow-xs backdrop-blur-xs">Habis</span>
        @elseif (! $habis && $b->stok_barang <= $b->stok_min_barang)
            <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-bold text-white shadow-xs">Stok Terbatas</span>
        @elseif ($isBaru)
            <span class="rounded-full bg-blue-600 px-2 py-0.5 text-[10px] font-bold text-white shadow-xs">Baru</span>
        @endif
    </div>

    {{-- Image Container with Zoom & Quick View trigger --}}
    <a href="{{ route('toko.produk', $b->id_barang) }}" class="block">
        <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700/60">
            <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
            
            {{-- Quick View Hover Button --}}
            <button type="button" @click.stop.prevent="quickView = true"
                    class="absolute inset-x-3 bottom-3 flex items-center justify-center gap-1.5 rounded-lg bg-white/90 py-1.5 text-xs font-semibold text-navy shadow-md backdrop-blur-xs opacity-0 transition-all duration-200 group-hover:opacity-100 hover:bg-white dark:bg-slate-800/95 dark:text-white dark:hover:bg-slate-800">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Lihat Cepat
            </button>
        </div>
        <p class="mt-3 text-sm font-semibold leading-snug text-slate-800 transition group-hover:text-navy dark:text-slate-100 dark:group-hover:text-kuning line-clamp-2">
            {{ $b->nama_barang }}
        </p>
    </a>

    <div class="mt-1 flex items-baseline justify-between">
        <p class="font-bold text-navy dark:text-kuning text-base">
            {{ \App\Support\Rupiah::format($b->harga_jual_barang) }}
        </p>
        @if (! $habis && $b->stok_barang <= $b->stok_min_barang)
            <p class="text-[11px] font-medium text-amber-600 dark:text-amber-400">Sisa {{ $b->stok_barang }}</p>
        @endif
    </div>

    {{-- Action Button --}}
    <div class="mt-auto pt-3" x-data="{ loading: false }">
        @if (auth()->user()?->isAnggota())
            <form method="POST" action="{{ route('toko.keranjang.tambah') }}"
                  @submit.prevent="
                      if (loading) return;
                      loading = true;
                      window.tambahKeranjang($event, {{ $b->id_barang }}, 1, '{{ addslashes($b->nama_barang) }}')
                          .finally(() => { loading = false; });
                  ">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $b->id_barang }}">
                <button type="submit" @disabled($habis)
                        class="btn-navy w-full gap-2 text-xs font-semibold transition-all duration-200 hover:shadow-md active:scale-95 disabled:cursor-not-allowed disabled:opacity-50">
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
            @if ($habis)
                <span class="btn-navy w-full cursor-not-allowed text-xs font-semibold opacity-50">Stok Habis</span>
            @else
                <a href="{{ route('login') }}" class="btn-navy w-full gap-2 text-xs font-semibold transition-all duration-200 hover:shadow-md active:scale-95">
                    <x-ikon nama="keranjang" class="h-4 w-4" /> Tambah ke Keranjang
                </a>
            @endif
        @endif
    </div>

    {{-- Quick View Modal --}}
    <div x-show="quickView" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         aria-modal="true" role="dialog">
        <div x-show="quickView"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
             @click="quickView = false"></div>

        <div x-show="quickView"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <button type="button" @click="quickView = false" class="absolute right-4 top-4 rounded-lg p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="grid gap-5 sm:grid-cols-2 items-center">
                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700">
                    <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" class="h-full w-full object-cover">
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy dark:text-white">{{ $b->nama_barang }}</h3>
                    <p class="mt-1 text-xs text-slate-400">Kode: {{ $b->kode_barang }}</p>
                    <p class="mt-3 text-xl font-bold text-navy dark:text-kuning">{{ \App\Support\Rupiah::format($b->harga_jual_barang) }}</p>
                    <p class="mt-1 text-xs {{ $habis ? 'text-red-500 font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                        {{ $habis ? 'Stok Tidak Tersedia' : 'Sisa Stok: ' . $b->stok_barang . ' ' . ($b->satuan_barang ?? 'item') }}
                    </p>
                    @if ($b->deskripsi_barang)
                        <p class="mt-3 text-xs text-slate-600 dark:text-slate-300 line-clamp-3 leading-relaxed">{{ $b->deskripsi_barang }}</p>
                    @endif
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('toko.produk', $b->id_barang) }}" class="btn border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            Halaman Detail
                        </a>
                        @if (auth()->user()?->isAnggota() && ! $habis)
                            <form method="POST" action="{{ route('toko.keranjang.tambah') }}"
                                  @submit.prevent="
                                      window.tambahKeranjang($event, {{ $b->id_barang }}, 1, '{{ addslashes($b->nama_barang) }}');
                                      quickView = false;
                                  ">
                                @csrf
                                <input type="hidden" name="id_barang" value="{{ $b->id_barang }}">
                                <button type="submit" class="btn-navy text-xs font-semibold">Beli</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
