@extends('layouts.admin')
@section('judul', 'Stok Barang')

@section('isi')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-navy">Stok Barang</h1>
        <nav class="mt-1 flex gap-4 text-sm"><span class="font-semibold text-navy">Daftar Stok</span><a href="{{ route('admin.stok.riwayat') }}" class="text-slate-500 hover:text-navy">Riwayat Stok</a></nav>
    </div>
    <a href="{{ route('admin.pembelian.create') }}" class="btn-kuning">+ Pembelian Barang</a>
</div>

<form method="GET" class="mt-4 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <input name="q" value="{{ request('q') }}" placeholder="Cari kode / nama" class="input max-w-xs">
    @foreach ([['', 'Semua'], ['menipis', 'Menipis (' . $jumlahMenipis . ')'], ['habis', 'Habis']] as [$k, $label])
        <a href="{{ route('admin.stok.index', array_filter(['filter' => $k, 'q' => request('q')])) }}" class="rounded-full px-4 py-1.5 text-sm font-medium {{ ($filter ?? '') === $k ? 'bg-navy text-white' : 'border text-slate-600' }}">{{ $label }}</a>
    @endforeach
    @if ($filter) <input type="hidden" name="filter" value="{{ $filter }}"> @endif
    <button class="btn-navy ml-auto">Cari</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[640px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3 font-medium">Barang</th><th class="font-medium">Kategori</th><th class="text-right font-medium">Stok</th><th class="text-right font-medium">Minimum</th><th class="font-medium">Status</th><th></th></tr></thead>
        <tbody class="divide-y">
        @forelse ($barang as $b)
            <tr>
                <td class="px-4 py-2.5"><p class="font-medium">{{ $b->nama_barang }}</p><p class="font-mono text-xs text-slate-400">{{ $b->kode_barang }}</p></td>
                <td>{{ $b->kategori?->nama_kategori ?? '-' }}</td>
                <td class="text-right font-semibold {{ $b->stok_barang <= $b->stok_min_barang ? 'text-red-600' : '' }}">{{ $b->stok_barang }}</td>
                <td class="text-right text-slate-500">{{ $b->stok_min_barang }}</td>
                <td><x-status-stok :stok="$b->stok_barang" :min="$b->stok_min_barang" /></td>
                <td class="pr-4 text-right"><a href="{{ route('admin.stok.edit', $b) }}" class="text-xs font-medium text-navy hover:underline">Sesuaikan</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-slate-400">Tidak ada barang.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $barang->links() }}</div>
@endsection
