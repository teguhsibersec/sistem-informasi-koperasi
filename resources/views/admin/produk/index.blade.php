@extends('layouts.admin')
@section('judul', 'Produk')

@section('isi')
@php $rp = fn ($n) => \App\Support\Rupiah::format($n); @endphp
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-navy">Produk</h1>
    <a href="{{ route('admin.produk.create') }}" class="btn-kuning">+ Tambah Produk</a>
</div>

<form method="GET" class="mt-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Cari kode / nama" class="input lg:col-span-2">
    <select name="kategori" class="input">
        <option value="">Semua kategori</option>
        @foreach ($kategori as $k) <option value="{{ $k->id_kategori }}" @selected((string) request('kategori') === (string) $k->id_kategori)>{{ $k->nama_kategori }}</option> @endforeach
    </select>
    <select name="status" class="input">
        <option value="">Semua status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
    </select>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="menipis" value="1" @checked(request()->boolean('menipis')) class="rounded border-slate-300"> Stok menipis</label>
    <button class="btn-navy">Filter</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[820px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr><th class="px-4 py-3 font-medium">Produk</th><th class="font-medium">Kategori</th><th class="font-medium">Harga</th><th class="font-medium">Stok</th><th class="font-medium">Status</th><th></th></tr>
        </thead>
        <tbody class="divide-y">
        @forelse ($produk as $b)
            <tr>
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-sm font-bold text-slate-400">
                            <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" loading="lazy" class="h-full w-full object-cover">
                        </div>
                        <div><p class="font-medium">{{ $b->nama_barang }}</p><p class="font-mono text-xs text-slate-400">{{ $b->kode_barang }}</p></div>
                    </div>
                </td>
                <td>{{ $b->kategori?->nama_kategori ?? '-' }}</td>
                <td class="whitespace-nowrap">{{ $rp($b->harga_jual_barang) }}</td>
                <td><a href="{{ route('admin.stok.edit', $b) }}" class="hover:underline">{{ $b->stok_barang }}</a> <x-status-stok :stok="$b->stok_barang" :min="$b->stok_min_barang" /></td>
                <td>{!! $b->status_barang === 'aktif' ? '<span class="text-sukses">Aktif</span>' : '<span class="text-slate-400">Nonaktif</span>' !!}</td>
                <td class="whitespace-nowrap pr-4 text-right">
                    <a href="{{ route('admin.produk.edit', $b) }}" class="text-xs font-medium text-navy hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.produk.destroy', $b) }}" class="inline" onsubmit="return confirm('Hapus produk {{ $b->nama_barang }}? Riwayat transaksi tetap tersimpan.')">
                        @csrf @method('DELETE') <button class="ml-2 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada produk.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $produk->links() }}</div>
@endsection
