@extends('layouts.admin')
@section('judul', 'Data Anggota')

@section('isi')
<div class="flex flex-wrap items-center justify-between gap-2">
    <h1 class="text-2xl font-bold text-navy">Data Anggota</h1>
    <a href="{{ route('admin.anggota.laporan', ['q' => $q]) }}" target="_blank" class="btn-kuning">Laporan (PDF)</a>
</div>

<form method="POST" action="{{ route('admin.anggota.store') }}" class="mt-4 space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf
    <p class="font-semibold text-navy">Tambah Anggota <span class="text-xs font-normal text-slate-400">(Kode Anggota dibuat otomatis)</span></p>
    @include('admin.anggota._form', ['a' => null])
    <button class="btn-navy">Simpan</button>
</form>

<form method="GET" class="mt-4 flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Cari kode, NIS, nama, atau kelas..." class="input">
    <button class="btn-navy">Cari</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500"><tr>
            <th class="px-4 py-3 font-medium">Kode Anggota</th><th class="font-medium">NIS</th><th class="font-medium">Nama Anggota</th><th class="font-medium">Kelas</th>
            <th class="font-medium">Alamat Anggota</th><th class="font-medium">Kota</th><th class="font-medium">Telepon</th><th class="font-medium">Status</th><th></th>
        </tr></thead>
        <tbody class="divide-y">
        @forelse ($anggota as $a)
            <tr>
                <td class="px-4 py-3 font-mono text-xs">{{ $a->no_anggota }}</td>
                <td>{{ $a->nis_anggota }}</td>
                <td>{{ $a->nama_anggota }}</td>
                <td>{{ $a->kelas_anggota }}</td>
                <td>{{ $a->alamat_anggota ?? '-' }}</td>
                <td>{{ $a->kota_anggota ?? '-' }}</td>
                <td>{{ $a->no_hp_anggota ?? '-' }}</td>
                <td>{!! $a->status_anggota === 'aktif' ? '<span class="text-sukses">Aktif</span>' : '<span class="text-slate-400">Nonaktif</span>' !!}</td>
                <td class="whitespace-nowrap pr-4 text-right">
                    <a href="{{ route('admin.anggota.edit', $a) }}" class="text-xs font-medium text-navy hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.anggota.destroy', $a) }}" class="inline" onsubmit="return confirm('Hapus anggota {{ $a->nama_anggota }}?')">
                        @csrf @method('DELETE') <button class="ml-2 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="py-10 text-center text-slate-400">Belum ada data anggota.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $anggota->links() }}</div>
@endsection
