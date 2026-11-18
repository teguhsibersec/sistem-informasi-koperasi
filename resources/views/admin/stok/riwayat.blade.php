@extends('layouts.admin')
@section('judul', 'Riwayat Stok')

@section('isi')
<h1 class="text-2xl font-bold text-navy">Stok Barang</h1>
<nav class="mt-1 flex gap-4 text-sm"><a href="{{ route('admin.stok.index') }}" class="text-slate-500 hover:text-navy">Daftar Stok</a><span class="font-semibold text-navy">Riwayat Stok</span></nav>

<form method="GET" class="mt-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Barang / no. nota / faktur" class="input lg:col-span-2">
    <select name="tipe" class="input"><option value="">Semua jenis</option>@foreach ($tipe as $k => $v) <option value="{{ $k }}" @selected(request('tipe') === $k)>{{ $v }}</option> @endforeach</select>
    <input type="date" name="dari" value="{{ request('dari') }}" class="input" aria-label="Dari tanggal">
    <input type="date" name="sampai" value="{{ request('sampai') }}" class="input" aria-label="Sampai tanggal">
    <button class="btn-navy">Filter</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3 font-medium">Waktu</th><th class="font-medium">Barang</th><th class="font-medium">Jenis</th><th class="text-right font-medium">Perubahan</th><th class="text-right font-medium">Sebelum → Sesudah</th><th class="font-medium">Referensi / Alasan</th><th class="font-medium">Pengurus</th></tr></thead>
        <tbody class="divide-y">
        @forelse ($riwayat as $r)
            <tr>
                <td class="whitespace-nowrap px-4 py-2.5">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $r->barang?->nama_barang ?? '-' }}<span class="block font-mono text-xs text-slate-400">{{ $r->barang?->kode_barang }}</span></td>
                <td>{{ $tipe[$r->tipe] ?? $r->tipe }}</td>
                <td class="text-right font-semibold {{ $r->qty_perubahan < 0 ? 'text-red-600' : 'text-sukses' }}">{{ $r->qty_perubahan > 0 ? '+' : '' }}{{ $r->qty_perubahan }}</td>
                <td class="text-right">{{ $r->stok_sebelum }} → {{ $r->stok_sesudah }}</td>
                <td class="text-xs text-slate-500">{{ $r->referensi }}{{ $r->referensi && $r->catatan ? ' · ' : '' }}{{ $r->catatan }}</td>
                <td class="text-xs">{{ $r->pengurus?->nama_pengurus ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-10 text-center text-slate-400">Tidak ada riwayat.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $riwayat->links() }}</div>
@endsection
