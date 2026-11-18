@extends('layouts.admin')
@section('judul', 'Transaksi')

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $metode = ['cash' => 'Cash', 'qris' => 'QRIS'];
@endphp
<h1 class="text-2xl font-bold text-navy">Transaksi</h1>

<form method="GET" class="mt-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-7">
    <input name="q" value="{{ request('q') }}" placeholder="No. nota" class="input lg:col-span-2">
    <input type="date" name="dari" value="{{ request('dari') }}" class="input" aria-label="Dari tanggal">
    <input type="date" name="sampai" value="{{ request('sampai') }}" class="input" aria-label="Sampai tanggal">
    <select name="status" class="input">
        <option value="">Semua status</option>
        @foreach ($daftarStatus as $k => $v)
            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <select name="metode" class="input">
        <option value="">Semua metode</option>
        @foreach ($metode as $k => $v)
            <option value="{{ $k }}" @selected(request('metode') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <button class="btn-navy">Filter</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                <th class="px-4 py-3 font-medium">No. Nota</th>
                <th class="font-medium">Waktu</th>
                <th class="font-medium">Pembeli</th>
                <th class="font-medium">Item</th>
                <th class="font-medium">Total</th>
                <th class="font-medium">Metode</th>
                <th class="font-medium">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @forelse ($transaksi as $t)
            <tr>
                <td class="px-4 py-3 font-mono text-xs">{{ $t->no_nota_penjualan }}</td>
                <td class="whitespace-nowrap">{{ $t->tgl_penjualan->format('d/m/Y H:i') }}</td>
                <td>{{ $t->anggota?->nama_anggota ?? 'Umum' }}</td>
                <td>{{ $t->details->sum('qty_detail_penjualan') }}</td>
                <td class="whitespace-nowrap">{{ $rp($t->total) }}</td>
                <td>{{ $metode[$t->pembayaranTerakhir?->metode_pembayaran] ?? '-' }}</td>
                <td><x-status-penjualan :status="$t->status_penjualan" /></td>
                <td class="pr-4 text-right"><a href="{{ route('admin.transaksi.show', $t) }}" class="text-xs font-medium text-navy hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $transaksi->links() }}</div>
@endsection
