@extends('layouts.toko')
@section('judul', 'Detail Pesanan ' . $penjualan->no_nota_penjualan)

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $langkah = [
        'menunggu_bayar' => ['Menunggu Bayar', 'Menunggu konfirmasi pembayaran'],
        'diproses' => ['Diproses', 'Pengurus sedang menyiapkan barang'],
        'siap_diambil' => ['Siap Diambil', 'Barang siap diambil di loket koperasi'],
        'selesai' => ['Selesai', 'Pesanan telah diambil oleh siswa'],
    ];
    $posisi = array_search($penjualan->status_penjualan, array_keys($langkah), true);
@endphp

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('toko.pesanan') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-navy hover:underline dark:text-kuning">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Pesanan Saya
    </a>
</div>

<div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-5 shadow-xs dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
    <div>
        <div class="flex items-center gap-2">
            <span class="font-mono text-lg font-bold text-navy dark:text-white">{{ $penjualan->no_nota_penjualan }}</span>
            <span class="text-xs text-slate-400">·</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $penjualan->tgl_penjualan->format('d/m/Y H:i') }} WIB</span>
        </div>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Dipesan oleh <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $penjualan->anggota?->nama_anggota ?? 'Anggota Koperasi' }}</span></p>
    </div>
    <div class="flex items-center gap-2">
        <x-status-penjualan :status="$penjualan->status_penjualan" />
    </div>
</div>

{{-- Visual Status Timeline --}}
<section class="mt-4 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-700 dark:bg-slate-800">
    @if ($penjualan->status_penjualan === 'batal')
        <div class="flex items-center gap-3 rounded-2xl bg-red-50 p-4 text-sm text-red-600 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-900">
            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-bold">Pesanan Dibatalkan</p>
                <p class="text-xs text-red-500">{{ $penjualan->catatan_penjualan ?: 'Pesanan ini telah dibatalkan. Stok barang telah dikembalikan ke etalase.' }}</p>
            </div>
        </div>
    @else
        <div class="relative">
            <div class="hidden sm:block absolute top-1/2 left-0 right-0 h-1 -translate-y-1/2 bg-slate-100 dark:bg-slate-700 -z-0">
                <div class="h-full bg-emerald-500 transition-all duration-500"
                     style="width: {{ $posisi === false ? 0 : ($posisi / (count($langkah) - 1)) * 100 }}%"></div>
            </div>

            <ol class="relative z-10 grid grid-cols-2 gap-4 sm:grid-cols-4 text-center">
                @foreach ($langkah as $kunci => [$label, $desk])
                    @php
                        $capai = $posisi !== false && $loop->index <= $posisi;
                        $aktif = $penjualan->status_penjualan === $kunci;
                    @endphp
                    <li class="flex flex-col items-center">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl transition-all duration-300 {{ $capai ? ($aktif ? 'bg-navy text-kuning shadow-lg ring-4 ring-navy/15 dark:bg-blue-600 dark:ring-blue-500/20' : 'bg-emerald-500 text-white shadow-sm') : 'bg-slate-100 text-slate-400 dark:bg-slate-700' }}">
                            @if ($capai && ! $aktif)
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <span class="font-bold text-xs">{{ $loop->iteration }}</span>
                            @endif
                        </span>
                        <p class="mt-2 text-xs font-bold {{ $aktif ? 'text-navy dark:text-kuning' : ($capai ? 'text-slate-800 dark:text-slate-100' : 'text-slate-400') }}">
                            {{ $label }}
                        </p>
                        <p class="hidden sm:block mt-0.5 text-[10px] text-slate-400 max-w-[120px]">{{ $desk }}</p>
                    </li>
                @endforeach
            </ol>
        </div>

        @if ($penjualan->status_penjualan === 'siap_diambil')
            {{-- QR Code Pengambilan Pesanan --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center gap-5 rounded-2xl bg-emerald-50/80 p-5 border border-emerald-200 dark:border-emerald-800 dark:bg-emerald-950/40">
                <div class="flex h-28 w-28 shrink-0 items-center justify-center rounded-2xl bg-white p-2.5 shadow-md border border-emerald-100">
                    {{-- QR Code Pattern --}}
                    <svg class="h-full w-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                        <path d="M10 10h30v30h-30z M15 15h20v20h-20z M20 20h10v10h-10z M60 10h30v30h-30z M65 15h20v20h-20z M70 20h10v10h-10z M10 60h30v30h-30z M15 65h20v20h-20z M20 70h10v10h-10z M45 10h10v10h-10z M45 30h10v10h-10z M10 45h10v10h-10z M30 45h10v10h-10z M50 45h20v10h-20z M80 45h10v10h-10z M45 60h10v20h-10z M60 60h10v10h-10z M80 60h10v10h-10z M60 80h20v10h-20z M80 80h10v10h-10z"/>
                    </svg>
                </div>
                <div>
                    <span class="rounded-full bg-emerald-200/60 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Siap Diambil</span>
                    <h3 class="mt-1 text-sm font-bold text-emerald-900 dark:text-emerald-100">Pesanan Anda Siap Diambil di Koperasi Sekolah!</h3>
                    <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">
                        Tunjukkan nomor nota <strong class="font-mono">{{ $penjualan->no_nota_penjualan }}</strong> atau pindai QR code di atas kepada pengurus koperasi di loket pengambilan.
                    </p>
                </div>
            </div>
        @endif
    @endif
</section>

{{-- Detail Barang & Ringkasan Pembayaran --}}
<div class="mt-4 grid gap-4 lg:grid-cols-3">
    {{-- Rincian Produk --}}
    <section class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800 lg:col-span-2">
        <h2 class="text-sm font-bold text-navy dark:text-white mb-3">Rincian Barang yang Dipesan</h2>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach ($penjualan->details as $d)
                <div class="flex items-center gap-3 py-3">
                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700">
                        <img src="{{ $d->barang->gambar_url }}" alt="{{ $d->barang->nama_barang }}" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-800 dark:text-white">{{ $d->barang->nama_barang }}</p>
                        <p class="text-[11px] text-slate-400">{{ $d->qty_detail_penjualan }} x {{ $rp($d->harga_satuan_detail_penjualan) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-navy dark:text-kuning">{{ $rp($d->subtotal) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 border-t border-slate-100 pt-4 dark:border-slate-700 flex justify-between items-center">
            <span class="text-xs font-semibold text-slate-500">Total Pembayaran:</span>
            <span class="text-lg font-black text-navy dark:text-kuning">{{ $rp($penjualan->total) }}</span>
        </div>
    </section>

    {{-- Detail Info & Actions --}}
    <aside class="space-y-4">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 text-xs shadow-xs dark:border-slate-700 dark:bg-slate-800 space-y-3">
            <h3 class="text-sm font-bold text-navy dark:text-white border-b border-slate-100 pb-2 dark:border-slate-700">Informasi Transaksi</h3>
            
            <div class="flex justify-between">
                <span class="text-slate-400">Tanggal Transaksi</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $penjualan->tgl_penjualan->format('d/m/Y H:i') }} WIB</span>
            </div>

            @if ($bayar)
                <div class="flex justify-between">
                    <span class="text-slate-400">Metode Pembayaran</span>
                    <span class="font-bold text-navy dark:text-white">{{ strtoupper($bayar->metode_pembayaran) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Status Pembayaran</span>
                    <x-status-pembayaran :status="$bayar->status_pembayaran" />
                </div>
            @endif

            @if ($bayar && $bayar->metode_pembayaran === 'cash' && $bayar->status_pembayaran === 'menunggu')
                <div class="rounded-xl bg-amber-50 p-3 text-[11px] text-amber-800 dark:bg-amber-950/40 dark:text-amber-200 border border-amber-200 dark:border-amber-900">
                    Silakan bayar tunai di kasir koperasi saat Anda mengambil pesanan.
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-700 dark:bg-slate-800 space-y-2">
            @if ($bolehBayar)
                <a href="{{ route('toko.pesanan.bayar', $penjualan) }}" class="btn-kuning w-full text-xs font-bold shadow-md animate-pulse">
                    Bayar Sekarang (QRIS)
                </a>
            @endif
            @if ($penjualan->status_penjualan !== 'batal')
                <a href="{{ route('toko.pesanan.nota', $penjualan) }}" class="btn w-full border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    Lihat / Cetak Struk Nota
                </a>
            @endif
            <a href="{{ route('toko.belanja') }}" class="btn border border-slate-200 bg-white w-full text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200">
                Pesan Barang Lainnya
            </a>
            @if ($bolehBatal)
                <form method="POST" action="{{ route('toko.pesanan.batal', $penjualan) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan otomatis dikembalikan.')">
                    @csrf
                    <button class="w-full rounded-xl border border-red-200 bg-white px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:border-red-900 dark:bg-slate-800 dark:text-red-400">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </aside>
</div>
@endsection
