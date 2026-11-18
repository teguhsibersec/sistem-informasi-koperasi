@extends('layouts.toko')
@section('judul', 'Keranjang')

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $itemsData = $isi->map(function ($x) {
        return [
            'id_barang' => (int) $x['id_barang'],
            'nama' => $x['barang']?->nama_barang ?? 'Barang',
            'harga' => (int) $x['harga'],
            'qty' => (int) $x['qty'],
            'subtotal' => (int) $x['subtotal'],
            'tersedia' => (bool) ($x['tersedia'] && $x['qty'] <= $x['maks']),
        ];
    })->values();
@endphp

<div class="mb-4 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-navy dark:text-white">Keranjang Belanja</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Pilih produk yang ingin dicheckout atau kelola kuantitas belanja Anda.</p>
    </div>
    @if ($isi->isNotEmpty())
        <span class="rounded-full bg-navy/10 px-3 py-1 text-xs font-semibold text-navy dark:bg-blue-900/50 dark:text-blue-300">
            Total {{ $isi->count() }} jenis barang
        </span>
    @endif
</div>

@if ($isi->isEmpty())
    <div class="mt-4 rounded-2xl border border-slate-200/80 bg-white py-16 px-4 text-center shadow-sm dark:border-slate-700/80 dark:bg-slate-800">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-navy/5 text-navy dark:bg-kuning/10 dark:text-kuning">
            <x-ikon nama="keranjang" class="h-10 w-10" />
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-800 dark:text-white">Keranjang Anda Masih Kosong</h3>
        <p class="mx-auto mt-1 max-w-sm text-xs text-slate-500 dark:text-slate-400">
            Belum ada produk yang dimasukkan ke keranjang belanja. Yuk jelajahi etalase koperasi sekarang!
        </p>
        <a href="{{ route('toko.belanja') }}" class="btn-kuning mt-5 inline-flex items-center gap-2 text-sm font-bold shadow-md hover:shadow-lg">
            <span>Mulai Belanja</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
@else
    <div x-data="{
        items: {{ Js::from($itemsData) }},
        terpilih: {{ Js::from($itemsData->where('tersedia', true)->pluck('id_barang')->values()) }},
        get total() {
            return this.items
                .filter(i => this.terpilih.includes(i.id_barang))
                .reduce((acc, i) => acc + i.subtotal, 0);
        },
        get totalQty() {
            return this.items
                .filter(i => this.terpilih.includes(i.id_barang))
                .reduce((acc, i) => acc + i.qty, 0);
        },
        get semuaTerpilih() {
            const tersedia = this.items.filter(i => i.tersedia);
            return tersedia.length > 0 && tersedia.every(i => this.terpilih.includes(i.id_barang));
        },
        toggleSemua() {
            const tersedia = this.items.filter(i => i.tersedia);
            if (this.semuaTerpilih) {
                this.terpilih = [];
            } else {
                this.terpilih = tersedia.map(i => i.id_barang);
            }
        },
        formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        },
        get checkoutUrl() {
            const base = '{{ route('toko.checkout') }}';
            const tersedia = this.items.filter(i => i.tersedia);
            if (this.terpilih.length === 0) return '#';
            if (this.terpilih.length === tersedia.length) return base;
            return base + '?items=' + this.terpilih.join(',');
        }
    }" class="grid gap-5 lg:grid-cols-3">

        {{-- Daftar Item Keranjang --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-700/80 dark:bg-slate-800 lg:col-span-2 overflow-hidden">
            {{-- Header Pilih Semua --}}
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-4 py-3 sm:px-5 dark:border-slate-700 dark:bg-slate-800/70">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox"
                           :checked="semuaTerpilih"
                           @change="toggleSemua()"
                           class="h-4 w-4 rounded border-slate-300 text-navy focus:ring-navy/20 dark:border-slate-600 dark:bg-slate-700 dark:focus:ring-kuning/20">
                    <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white">
                        Pilih Semua (<span x-text="terpilih.length"></span>/<span x-text="items.filter(i => i.tersedia).length"></span>)
                    </span>
                </label>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                    Centang untuk checkout
                </span>
            </div>

            {{-- List Barang --}}
            <div class="divide-y divide-slate-100 dark:divide-slate-700/60 p-2 sm:p-3">
                @foreach ($isi as $x)
                    @php
                        $b = $x['barang'];
                        $isReady = $x['tersedia'] && $x['qty'] <= $x['maks'];
                    @endphp
                    <div class="flex flex-col gap-3 p-3 transition-colors rounded-xl hover:bg-slate-50/70 dark:hover:bg-slate-700/40 sm:flex-row sm:items-center sm:gap-4"
                         :class="{ 'opacity-60 bg-slate-50/40 dark:bg-slate-900/20': !{{ $isReady ? 'true' : 'false' }} }">
                        
                        {{-- Checkbox --}}
                        <div class="flex items-center gap-3 shrink-0">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox"
                                       :value="{{ $x['id_barang'] }}"
                                       x-model="terpilih"
                                       @disabled(! $isReady)
                                       class="h-4 w-4 rounded border-slate-300 text-navy focus:ring-navy/20 dark:border-slate-600 dark:bg-slate-700 dark:focus:ring-kuning/20 disabled:cursor-not-allowed disabled:opacity-40">
                            </label>

                            {{-- Gambar Barang --}}
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-100 dark:border-slate-700">
                                @if ($b)
                                    <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" loading="lazy" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center font-bold text-slate-400">?</div>
                                @endif
                            </div>
                        </div>

                        {{-- Info Barang --}}
                        <div class="min-w-0 flex-1">
                            <a href="{{ $b ? route('toko.produk', $b->id_barang) : '#' }}" class="font-semibold text-slate-800 hover:text-navy dark:text-white dark:hover:text-kuning transition-colors line-clamp-1 text-sm">
                                {{ $b?->nama_barang ?? 'Barang telah dihapus' }}
                            </a>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $rp($x['harga']) }} <span class="text-slate-400">/ pcs</span>
                            </p>
                            @if (! $x['tersedia'])
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Barang tidak tersedia. Silakan hapus dari keranjang.
                                </p>
                            @elseif ($x['qty'] > $x['maks'])
                                <p class="mt-1 text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Stok tersisa {{ $x['maks'] }}. Mohon kurangi jumlah.
                                </p>
                            @endif
                        </div>

                        {{-- Controls: Ubah Qty, Subtotal & Hapus --}}
                        <div class="flex items-center justify-between gap-3 sm:justify-end">
                            @if ($x['tersedia'])
                                <form method="POST" action="{{ route('toko.keranjang.ubah', $x['id_barang']) }}" class="flex items-center gap-1.5">
                                    @csrf @method('PATCH')
                                    <input type="number" name="qty" value="{{ $x['qty'] }}" min="1" max="{{ $x['maks'] }}"
                                           class="w-16 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-center text-xs font-semibold text-slate-800 focus:border-navy focus:outline-none focus:ring-1 focus:ring-navy dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                           aria-label="Jumlah">
                                    <button type="submit" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:border-navy hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:border-kuning dark:hover:bg-slate-700">
                                        Update
                                    </button>
                                </form>
                            @endif

                            <div class="min-w-24 text-right">
                                <span class="text-xs text-slate-400 block sm:hidden">Subtotal:</span>
                                <span class="text-sm font-bold text-navy dark:text-kuning">{{ $rp($x['subtotal']) }}</span>
                            </div>

                            <form method="POST" action="{{ route('toko.keranjang.hapus', $x['id_barang']) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400"
                                        title="Hapus dari keranjang" aria-label="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Aside Ringkasan Belanja --}}
        <aside class="h-fit rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-800">
            <h2 class="font-bold text-slate-800 dark:text-white text-base border-b border-slate-100 pb-3 dark:border-slate-700">Ringkasan Belanja</h2>
            
            <div class="mt-4 space-y-2.5 text-sm">
                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                    <span>Produk Dipilih</span>
                    <span class="font-semibold text-slate-800 dark:text-white">
                        <span x-text="terpilih.length"></span> dari <span x-text="items.filter(i => i.tersedia).length"></span> produk
                    </span>
                </div>
                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                    <span>Jumlah Kuantitas</span>
                    <span class="font-semibold text-slate-800 dark:text-white">
                        <span x-text="totalQty"></span> item
                    </span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                    <span class="font-bold text-slate-800 dark:text-white">Total Belanja</span>
                    <span class="text-2xl font-black text-navy dark:text-kuning" x-text="formatRupiah(total)">{{ $rp($total) }}</span>
                </div>
            </div>

            {{-- Checkout Button Reactive --}}
            <div class="mt-5">
                <template x-if="terpilih.length > 0">
                    <a :href="checkoutUrl" class="btn-kuning w-full py-3 text-sm font-bold shadow-md hover:shadow-lg transition-transform active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Lanjut ke Checkout</span>
                        <span class="rounded-full bg-navy/15 px-2 py-0.5 text-xs text-navy" x-text="terpilih.length"></span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </template>
                <template x-if="terpilih.length === 0">
                    <button type="button" disabled class="btn w-full py-3 cursor-not-allowed bg-slate-200 text-slate-400 dark:bg-slate-700 dark:text-slate-500 text-xs font-semibold">
                        Centang Produk Terlebih Dahulu
                    </button>
                </template>
            </div>

            <div class="mt-4 rounded-xl bg-blue-50/60 p-3 text-[11px] text-slate-600 dark:bg-slate-700/50 dark:text-slate-300 space-y-1">
                <p class="font-semibold text-navy dark:text-kuning flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informasi Pengambilan
                </p>
                <p>Pesanan dapat diambil di koperasi sekolah saat jam operasional. Tunjukkan kode pesanan atau QR nota Anda ke pengurus.</p>
            </div>

            <a href="{{ route('toko.belanja') }}" class="mt-4 block text-center text-xs font-semibold text-navy hover:underline dark:text-blue-300">
                ← Lanjut Belanja Produk Lain
            </a>
        </aside>
    </div>
@endif
@endsection
