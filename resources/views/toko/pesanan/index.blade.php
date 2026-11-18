@extends('layouts.toko')
@section('judul', 'Pesanan Saya')

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $metode = ['cash' => 'Tunai (Cash)', 'qris' => 'QRIS Dinamis'];
@endphp

<div x-data="{ tabFilter: 'semua' }">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/80 pb-4 dark:border-slate-700">
        <div>
            <h1 class="text-2xl font-extrabold text-navy dark:text-white tracking-tight">Pesanan Saya</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pantau progres pesanan perlengkapan sekolah dan riwayat belanja Anda.</p>
        </div>
        <a href="{{ route('toko.belanja') }}" class="btn-navy text-xs font-semibold">
            + Buat Pesanan Baru
        </a>
    </div>

    {{-- Tab Filter Status --}}
    <div class="mt-4 flex gap-1.5 overflow-x-auto pb-1">
        <button type="button" @click="tabFilter = 'semua'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'semua' ? 'bg-navy text-white shadow-xs dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Semua
        </button>
        <button type="button" @click="tabFilter = 'menunggu_bayar'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'menunggu_bayar' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Menunggu Bayar
        </button>
        <button type="button" @click="tabFilter = 'diproses'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'diproses' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Diproses
        </button>
        <button type="button" @click="tabFilter = 'siap_diambil'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'siap_diambil' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Siap Diambil
        </button>
        <button type="button" @click="tabFilter = 'selesai'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'selesai' ? 'bg-navy text-white shadow-xs dark:bg-blue-600' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Selesai
        </button>
        <button type="button" @click="tabFilter = 'batal'"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition"
                :class="tabFilter === 'batal' ? 'bg-red-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700'">
            Batal
        </button>
    </div>

    {{-- Daftar Pesanan --}}
    <div class="mt-4 space-y-3.5">
        @forelse ($pesanan as $p)
            @php
                $b = $p->pembayaranTerakhir;
                $bisaBayar = $b && $b->metode_pembayaran === 'qris' && $b->status_pembayaran === 'menunggu' && $p->status_penjualan === 'menunggu_bayar';
            @endphp
            <div x-show="tabFilter === 'semua' || tabFilter === '{{ $p->status_penjualan }}'"
                 class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-xs transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-navy dark:bg-blue-900/40 dark:text-kuning">
                            <x-ikon nama="transaksi" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="font-mono text-xs font-bold text-navy dark:text-white">{{ $p->no_nota_penjualan }}</p>
                            <p class="text-[11px] text-slate-400">{{ $p->tgl_penjualan->format('d/m/Y H:i') }} WIB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                            {{ $metode[$b?->metode_pembayaran] ?? 'Metode -' }}
                        </span>
                        <x-status-penjualan :status="$p->status_penjualan" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-1">
                            {{ $p->details->map(fn ($d) => $d->barang->nama_barang . ' (' . $d->qty_detail_penjualan . 'x)')->join(', ') }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $p->details->sum('qty_detail_penjualan') }} total barang</p>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Total Pesanan</p>
                        <p class="text-base font-extrabold text-navy dark:text-kuning">{{ $rp($p->total) }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-700">
                    @if ($bisaBayar)
                        <a href="{{ route('toko.pesanan.bayar', $p) }}" class="btn-kuning px-4 py-2 text-xs font-bold shadow-xs animate-pulse">
                            Bayar Sekarang (QRIS)
                        </a>
                    @endif
                    <a href="{{ route('toko.belanja') }}" class="btn border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200">
                        Pesan Lagi
                    </a>
                    <a href="{{ route('toko.pesanan.show', $p) }}" class="btn-navy px-4 py-2 text-xs font-semibold">
                        Lihat Detail Pesanan
                    </a>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white py-16 px-4 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 text-navy dark:bg-blue-900/40 dark:text-kuning shadow-inner">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="mt-4 text-base font-bold text-slate-800 dark:text-white">Belum ada pesanan.</p>
                <p class="mt-1 max-w-sm text-xs text-slate-400">Anda belum pernah melakukan pemesanan di koperasi sekolah.</p>
                <a href="{{ route('toko.belanja') }}" class="btn-kuning mt-5 font-bold text-navy shadow-sm">Mulai Belanja</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $pesanan->links() }}</div>
</div>
@endsection
