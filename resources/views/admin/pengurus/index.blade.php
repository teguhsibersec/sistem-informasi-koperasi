@extends('layouts.admin')
@section('judul', 'Data Pengurus')

@section('isi')
<div class="flex flex-wrap items-center justify-between gap-2">
    <h1 class="text-2xl font-bold text-navy">Data Pengurus</h1>
    <a href="{{ route('admin.pengurus.laporan', ['q' => $q]) }}" target="_blank" class="btn-kuning">Laporan (PDF)</a>
</div>
<p class="mt-1 text-sm text-slate-500">Jabatan menentukan akses: Ketua = semua menu, Bendahara = kasir, transaksi, pembayaran; Sekretaris = produk, stok, pembelian.</p>

<form method="POST" action="{{ route('admin.pengurus.store') }}" class="mt-4 space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf
    <p class="font-semibold text-navy">Tambah Pengurus</p>
    @include('admin.pengurus._form', ['p' => null])
    <button class="btn-navy">Simpan</button>
</form>

<form method="GET" class="mt-4 flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Cari ID, nama, atau jabatan..." class="input">
    <button class="btn-navy">Cari</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500"><tr>
            <th class="px-4 py-3 font-medium">ID Pengurus</th><th class="font-medium">Nama Pengurus</th><th class="font-medium">Jabatan</th>
            <th class="font-medium">Alamat Pengurus</th><th class="font-medium">Kota</th><th class="font-medium">Telepon</th><th></th>
        </tr></thead>
        <tbody class="divide-y">
        @forelse ($pengurus as $p)
            <tr>
                <td class="px-4 py-3 font-mono text-xs">{{ $p->kode_pengurus }}</td>
                <td>{{ $p->nama_pengurus }}</td>
                <td>{{ \App\Models\Pengurus::JABATAN[$p->jabatan_pengurus] ?? $p->jabatan_pengurus }}</td>
                <td>{{ $p->alamat_pengurus ?? '-' }}</td>
                <td>{{ $p->kota_pengurus ?? '-' }}</td>
                <td>{{ $p->telepon_pengurus ?? '-' }}</td>
                <td class="whitespace-nowrap pr-4 text-right">
                    <a href="{{ route('admin.pengurus.edit', $p) }}" class="text-xs font-medium text-navy hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.pengurus.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus pengurus {{ $p->nama_pengurus }}?')">
                        @csrf @method('DELETE') <button class="ml-2 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-10 text-center text-slate-400">Belum ada data pengurus.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pengurus->links() }}</div>
@endsection
