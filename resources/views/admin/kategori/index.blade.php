@extends('layouts.admin')
@section('judul', 'Kategori')

@section('isi')
<h1 class="text-2xl font-bold text-navy">Kategori</h1>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3 font-medium">Kategori</th><th class="font-medium">Slug</th><th class="font-medium">Produk</th><th></th></tr></thead>
            <tbody class="divide-y">
            @forelse ($kategori as $k)
                <tr>
                    <td class="px-4 py-3"><span class="flex items-center gap-2"><x-ikon :nama="$k->ikon ?? 'grid'" class="h-5 w-5 text-navy" /> {{ $k->nama_kategori }}</span></td>
                    <td class="font-mono text-xs text-slate-400">{{ $k->slug_kategori }}</td>
                    <td>{{ $k->barang_count }}</td>
                    <td class="whitespace-nowrap pr-4 text-right">
                        <a href="{{ route('admin.kategori.edit', $k) }}" class="text-xs font-medium text-navy hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus kategori {{ $k->nama_kategori }}?')">
                            @csrf @method('DELETE') <button class="ml-2 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-slate-400">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.kategori.store') }}" class="h-fit space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        @csrf
        <p class="font-semibold text-navy">Tambah Kategori</p>
        <div><label class="label" for="nama_kategori">Nama *</label><input id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" maxlength="50" required class="input"></div>
        <div>
            <label class="label" for="ikon">Ikon *</label>
            <select id="ikon" name="ikon" class="input">@foreach ($daftarIkon as $v => $l) <option value="{{ $v }}" @selected(old('ikon') === $v)>{{ $l }}</option> @endforeach</select>
        </div>
        <button class="btn-navy w-full">Simpan</button>
    </form>
</div>
@endsection
