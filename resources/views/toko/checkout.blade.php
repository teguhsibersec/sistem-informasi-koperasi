@extends('layouts.toko')
@section('judul', 'Checkout')

@section('isi')
@php $rp = fn ($n) => \App\Support\Rupiah::format($n); @endphp
<div class="mb-4 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-navy dark:text-white">Checkout</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Periksa detail pesanan dan tentukan metode pembayaran.</p>
    </div>
    <a href="{{ route('toko.keranjang') }}" class="text-xs font-semibold text-navy hover:underline dark:text-blue-300 flex items-center gap-1">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Keranjang
    </a>
</div>

<form method="POST" action="{{ route('toko.checkout.store') }}" class="grid gap-5 lg:grid-cols-3" x-data="{ metode: '{{ old('metode', 'qris') }}' }">
    @csrf
    @if (!empty($selectedItems))
        <input type="hidden" name="items" value="{{ $selectedItems }}">
    @endif

    <div class="space-y-4 lg:col-span-2">
        {{-- Section Daftar Pesanan --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    <x-ikon nama="keranjang" class="h-5 w-5 text-navy dark:text-kuning" />
                    <h2 class="font-bold text-slate-800 dark:text-white">Pesanan Anda</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                    {{ $isi->sum('qty') }} barang
                </span>
            </div>

            <div class="mt-3 divide-y divide-slate-100 text-sm dark:divide-slate-700/60">
                @foreach ($isi as $x)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-100 dark:border-slate-700">
                                @if ($x['barang'])
                                    <img src="{{ $x['barang']->gambar_url }}" alt="{{ $x['barang']->nama_barang }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-400 text-xs font-bold">?</div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $x['barang']?->nama_barang ?? 'Produk' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $x['qty'] }} × {{ $rp($x['harga']) }}
                                </p>
                            </div>
                        </div>
                        <span class="shrink-0 text-right font-bold text-navy dark:text-kuning">{{ $rp($x['subtotal']) }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section Metode Pembayaran --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-800">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 dark:border-slate-700">
                <x-ikon nama="tunai" class="h-5 w-5 text-navy dark:text-kuning" />
                <h2 class="font-bold text-slate-800 dark:text-white">Metode Pembayaran</h2>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                {{-- Opsi QRIS --}}
                <label class="relative flex cursor-pointer flex-col rounded-xl border-2 p-4 transition-all"
                       :class="metode === 'qris'
                           ? 'border-navy bg-navy/5 text-navy dark:border-kuning dark:bg-kuning/10 dark:text-white shadow-xs'
                           : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-slate-600'">
                    <input type="radio" name="metode" value="qris" x-model="metode" class="sr-only">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white p-1 shadow-2xs dark:border-slate-600 flex items-center justify-center">
                                <img src="{{ asset('images/pembayaran/qris.svg') }}" alt="QRIS" class="h-full w-auto object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block text-sm">QRIS Dinamis</span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Verifikasi Otomatis
                                </span>
                            </div>
                        </div>
                        <span class="h-5 w-5 rounded-full border-2 flex items-center justify-center transition-colors shrink-0"
                              :class="metode === 'qris' ? 'border-navy bg-navy dark:border-kuning dark:bg-kuning' : 'border-slate-300 dark:border-slate-600'">
                            <span x-show="metode === 'qris'" class="h-2 w-2 rounded-full bg-white dark:bg-navy"></span>
                        </span>
                    </div>
                    <p class="mt-2.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Bayar sekarang dengan e-wallet (GoPay, OVO, ShopeePay, DANA) atau mobile banking. QR berlaku <span class="font-semibold text-slate-800 dark:text-white">{{ $menit }} menit</span>.
                    </p>
                </label>

                {{-- Opsi Cash / COD --}}
                <label class="relative flex cursor-pointer flex-col rounded-xl border-2 p-4 transition-all"
                       :class="metode === 'cash'
                           ? 'border-navy bg-navy/5 text-navy dark:border-kuning dark:bg-kuning/10 dark:text-white shadow-xs'
                           : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-slate-600'">
                    <input type="radio" name="metode" value="cash" x-model="metode" class="sr-only">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white p-1 shadow-2xs dark:border-slate-600 flex items-center justify-center">
                                <img src="{{ asset('images/pembayaran/cod.svg') }}" alt="COD Cash" class="h-full w-auto object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block text-sm">Cash / COD</span>
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold">
                                    Bayar di Koperasi
                                </span>
                            </div>
                        </div>
                        <span class="h-5 w-5 rounded-full border-2 flex items-center justify-center transition-colors shrink-0"
                              :class="metode === 'cash' ? 'border-navy bg-navy dark:border-kuning dark:bg-kuning' : 'border-slate-300 dark:border-slate-600'">
                            <span x-show="metode === 'cash'" class="h-2 w-2 rounded-full bg-white dark:bg-navy"></span>
                        </span>
                    </div>
                    <p class="mt-2.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Bayar tunai saat mengambil pesanan langsung di koperasi sekolah. Jam layanan: <span class="font-semibold text-slate-800 dark:text-white">{{ str_replace('-', ' - ', $jamLayanan) }}</span>.
                    </p>
                </label>
            </div>

            {{-- Catatan Opsional --}}
            <div class="mt-5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="catatan">
                    Catatan Pesanan <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input id="catatan" name="catatan" maxlength="200" value="{{ old('catatan') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy/20 dark:border-slate-700 dark:bg-slate-900/60 dark:text-white dark:placeholder-slate-500 dark:focus:border-kuning dark:focus:ring-kuning/20"
                       placeholder="Contoh: Titip di loket koperasi / diambil saat istirahat">
            </div>
        </section>
    </div>

    {{-- Ringkasan Belanja --}}
    <aside class="h-fit rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-800">
        <h2 class="font-bold text-slate-800 dark:text-white text-base border-b border-slate-100 pb-3 dark:border-slate-700">Ringkasan Pembayaran</h2>
        
        <div class="mt-4 space-y-2.5 text-sm">
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                <span>Total Barang</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $isi->sum('qty') }} item</span>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                <span class="font-bold text-slate-800 dark:text-white">Total Tagihan</span>
                <span class="text-2xl font-black text-navy dark:text-kuning">{{ $rp($total) }}</span>
            </div>
        </div>

        <button type="submit" class="btn-kuning mt-5 w-full py-3 text-sm font-bold shadow-md hover:shadow-lg transition-transform active:scale-[0.99] flex items-center justify-center gap-2">
            <span>Buat Pesanan Sekarang</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>

        <p class="mt-3 text-center text-[11px] text-slate-400 dark:text-slate-400 leading-relaxed">
            Pesanan disimpan dan stok dicadangkan aman. Bukti pengambilan berupa kode pesanan & QR code.
        </p>

        <a href="{{ route('toko.keranjang') }}" class="mt-4 block text-center text-xs font-semibold text-navy hover:underline dark:text-blue-300">
            ← Kembali ke keranjang
        </a>
    </aside>
</form>
@endsection
