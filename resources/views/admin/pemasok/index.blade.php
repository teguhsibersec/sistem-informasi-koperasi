@extends('layouts.admin')
@section('judul', 'Pemasok')

@section('isi')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-navy">Pemasok</h1>
        <nav class="mt-1 flex gap-4 text-sm"><a href="{{ route('admin.pembelian.index') }}" class="text-slate-500 hover:text-navy">Pembelian</a><span class="font-semibold text-navy">Pemasok</span></nav>
    </div>
    <form method="GET" class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Cari pemasok" class="input"><button class="btn-navy">Cari</button></form>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3 font-medium">Pemasok</th><th class="font-medium">Telepon</th><th class="font-medium">Pembelian</th><th></th></tr></thead>
            <tbody class="divide-y">
            @forelse ($pemasok as $p)
                <tr>
                    <td class="px-4 py-3"><p class="font-medium">{{ $p->nama_pemasok }}</p><p class="text-xs text-slate-400">{{ $p->alamat_pemasok }}</p></td>
                    <td>{{ $p->telepon_pemasok ?? '-' }}</td>
                    <td><a href="{{ route('admin.pembelian.index', ['pemasok' => $p->id_pemasok]) }}" class="text-navy hover:underline">{{ $p->pembelian_count }}</a></td>
                    <td class="whitespace-nowrap pr-4 text-right">
                        <a href="{{ route('admin.pembelian.pemasok.edit', $p) }}" class="text-xs font-medium text-navy hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.pembelian.pemasok.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus pemasok {{ $p->nama_pemasok }}?')">
                            @csrf @method('DELETE') <button class="ml-2 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-slate-400">Belum ada pemasok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.pembelian.pemasok.store') }}" class="h-fit space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        @csrf
        <p class="font-semibold text-navy">Tambah Pemasok</p>
        <div><label class="label" for="nama_pemasok">Nama *</label><input id="nama_pemasok" name="nama_pemasok" value="{{ old('nama_pemasok') }}" maxlength="100" required class="input"></div>
        <div><label class="label" for="telepon_pemasok">Telepon</label><input id="telepon_pemasok" name="telepon_pemasok" value="{{ old('telepon_pemasok') }}" maxlength="15" class="input"></div>
        <div><label class="label" for="alamat_pemasok">Alamat</label><input id="alamat_pemasok" name="alamat_pemasok" value="{{ old('alamat_pemasok') }}" maxlength="200" class="input"></div>
        <button class="btn-navy w-full">Simpan</button>
    </form>
</div>
@endsection
