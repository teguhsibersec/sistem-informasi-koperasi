@extends('layouts.admin')
@section('judul', 'Dashboard Admin')

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $delta = function (?int $p, string $akhir) {
        if ($p === null) {
            return ['Belum ada data pembanding', 'text-slate-400'];
        }

        return $p >= 0 ? ["↑ {$p}% {$akhir}", 'text-sukses'] : ['↓ ' . abs($p) . "% {$akhir}", 'text-red-600'];
    };
    $baru = $ringkasan['produk']['baru'];
    $kartu = [
        ['Total Penjualan', $rp($ringkasan['penjualan']['hari_ini']), $delta($ringkasan['penjualan']['persen'], 'dari kemarin'), 'uang', 'bg-blue-100 text-blue-700'],
        ['Pesanan Hari Ini', number_format($ringkasan['pesanan']['hari_ini'], 0, ',', '.'), $delta($ringkasan['pesanan']['persen'], 'dari kemarin'), 'keranjang', 'bg-green-100 text-green-700'],
        ['Total Produk', number_format($ringkasan['produk']['total'], 0, ',', '.'), $baru > 0 ? ["↑ {$baru} produk baru", 'text-sukses'] : ['Tidak ada produk baru', 'text-slate-400'], 'produk', 'bg-yellow-100 text-yellow-700'],
        ['Anggota Terdaftar', number_format($ringkasan['anggota']['total'], 0, ',', '.'), $delta($ringkasan['anggota']['persen'], 'dari bulan lalu'), 'anggota', 'bg-purple-100 text-purple-700'],
    ];
    $metode = ['cash' => 'Cash', 'qris' => 'QRIS'];
@endphp

<h1 class="text-2xl font-bold text-navy">Dashboard Admin</h1>
<p class="text-sm text-slate-500">Selamat datang, {{ auth()->user()->name }}. Berikut adalah ringkasan aktivitas koperasi hari ini.</p>

{{-- Pengaturan Cepat Nomor WhatsApp Admin --}}
<div class="mt-4 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs">
    <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-inner">
            <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.662 1.435 5.183L2 22l4.954-1.399C8.423 21.493 10.15 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-navy">Nomor WhatsApp Admin Koperasi</h3>
                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aktif di Toko</span>
            </div>
            <p class="text-xs text-slate-500">Terhubung ke tombol melayang WhatsApp (*floating button*) toko publik & kontak siswa.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.no_admin.update') }}" class="flex flex-wrap items-center gap-2">
        @csrf
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-semibold text-slate-400">📞</span>
            <input type="text" name="no_admin" value="{{ old('no_admin', $noAdmin ?? \App\Models\Pengaturan::noAdmin()) }}"
                   required placeholder="Contoh: 081234567890"
                   class="w-48 sm:w-56 rounded-xl border border-slate-300 py-2 pl-8 pr-3 text-xs font-semibold text-slate-800 focus:border-navy focus:outline-none focus:ring-2 focus:ring-navy/20">
        </div>
        <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-bold shadow-xs transition">
            Simpan Nomor
        </button>
        <a href="{{ \App\Models\Pengaturan::noAdminWaLink('Halo Admin Koperasi') }}" target="_blank" rel="noopener noreferrer"
           class="btn border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 px-3 py-2 text-xs font-semibold transition"
           title="Tes Buka Chat WhatsApp">
            Tes Chat WA ↗
        </a>
    </form>
</div>

<div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($kartu as [$label, $nilai, [$teksDelta, $kelasDelta], $ikon, $warna])
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $warna }}"><x-ikon :nama="$ikon" class="h-5 w-5" /></div>
            <p class="mt-3 text-sm text-slate-600">{{ $label }}</p>
            <p class="text-2xl font-bold text-navy">{{ $nilai }}</p>
            <p class="mt-1 text-xs {{ $kelasDelta }}">{{ $teksDelta }}</p>
        </div>
    @endforeach
</div>

<section class="mt-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
         x-data="grafikPenjualan(@js(route('admin.grafik')), @js($grafik))">
    <div class="flex items-center justify-between gap-3">
        <h2 class="font-semibold text-navy">Grafik Penjualan (<span x-text="judul"></span>)</h2>
        <select x-model="rentang" @change="ganti()" :disabled="memuat" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
            <option value="7">7 Hari Terakhir</option>
            <option value="30">30 Hari Terakhir</option>
            <option value="bulan">Bulan Ini</option>
        </select>
    </div>
    <div class="mt-4 h-64"><canvas x-ref="kanvas"></canvas></div>
</section>

<div class="mt-5 grid gap-5 xl:grid-cols-5">
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm xl:col-span-3">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-navy">Transaksi Terbaru</h2>
            <a href="{{ route('admin.transaksi.index') }}" class="text-xs font-medium text-navy hover:underline">Lihat Semua →</a>
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs">
                <thead class="text-slate-500">
                    <tr class="border-b">
                        <th class="py-2 pr-2 font-medium">No</th>
                        <th class="pr-2 font-medium">Nama Anggota</th>
                        <th class="pr-2 font-medium">Produk</th>
                        <th class="pr-2 font-medium">Total</th>
                        <th class="pr-2 font-medium">Metode</th>
                        <th class="pr-2 font-medium">Status</th>
                        <th class="font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($transaksi as $i => $t)
                    <tr class="border-b last:border-0">
                        <td class="py-2.5 pr-2">{{ $i + 1 }}</td>
                        <td class="pr-2">{{ $t->anggota?->nama_anggota ?? 'Umum' }}</td>
                        <td class="pr-2">{{ \Illuminate\Support\Str::limit($t->details->map(fn ($d) => $d->barang->nama_barang)->implode(', '), 28) }}</td>
                        <td class="whitespace-nowrap pr-2">{{ $rp($t->total) }}</td>
                        <td class="pr-2">{{ $metode[$t->pembayaranTerakhir?->metode_pembayaran] ?? '-' }}</td>
                        <td class="pr-2"><x-status-penjualan :status="$t->status_penjualan" /></td>
                        <td class="whitespace-nowrap">{{ $t->tgl_penjualan->isToday() ? $t->tgl_penjualan->format('H:i') : $t->tgl_penjualan->format('d/m H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm xl:col-span-2">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-semibold text-navy">
                <x-ikon nama="peringatan" class="h-5 w-5 text-red-600" /> Stok Menipis
                @if ($totalMenipis > 0) <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-600">{{ $totalMenipis }}</span> @endif
            </h2>
            <a href="{{ route('admin.stok.index') }}" class="text-xs font-medium text-navy hover:underline">Lihat Semua →</a>
        </div>
        <div class="mt-3 divide-y">
            @forelse ($stokMenipis as $b)
                <a href="{{ route('admin.stok.edit', $b->id_barang) }}" class="flex items-center gap-3 py-2.5 hover:bg-slate-50">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-sm font-bold text-slate-400">
                        <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" loading="lazy" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ $b->nama_barang }}</p>
                        <p class="text-xs text-red-600">Stok: {{ $b->stok_barang }}</p>
                    </div>
                    <x-ikon nama="panah" class="h-4 w-4 text-slate-400" />
                </a>
            @empty
                <p class="py-6 text-center text-sm text-slate-400">Semua stok aman.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
