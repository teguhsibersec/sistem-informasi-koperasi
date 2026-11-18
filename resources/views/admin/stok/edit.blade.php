@extends('layouts.admin')
@section('judul', 'Sesuaikan Stok')

@section('isi')
<a href="{{ route('admin.stok.index') }}" class="text-xs text-navy hover:underline">← Daftar stok</a>
<h1 class="mt-1 text-2xl font-bold text-navy">{{ $barang->nama_barang }}</h1>
<p class="text-sm text-slate-500"><span class="font-mono">{{ $barang->kode_barang }}</span> · {{ $barang->kategori?->nama_kategori }}</p>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <form method="POST" action="{{ route('admin.stok.update', $barang) }}" class="h-fit space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        @csrf @method('PUT')
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">Stok tercatat</span>
            <span class="text-3xl font-bold {{ $barang->stok_barang <= $barang->stok_min_barang ? 'text-red-600' : 'text-navy' }}">{{ $barang->stok_barang }}</span>
        </div>
        <x-status-stok :stok="$barang->stok_barang" :min="$barang->stok_min_barang" />
        <div>
            <label class="label" for="stok_baru">Jumlah stok sebenarnya *</label>
            <input id="stok_baru" type="number" min="0" name="stok_baru" value="{{ old('stok_baru', $barang->stok_barang) }}" required class="input">
        </div>
        <div>
            <label class="label" for="alasan">Alasan *</label>
            <input id="alasan" name="alasan" value="{{ old('alasan') }}" maxlength="200" required class="input" placeholder="Mis. stok opname, barang rusak, selisih hitung">
        </div>
        <button class="btn-navy w-full">Simpan Penyesuaian</button>
        <p class="text-xs text-slate-400">Setiap penyesuaian tercatat di riwayat stok beserta alasan dan pengurusnya.</p>
    </form>

    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
        <h2 class="font-semibold text-navy">Riwayat Terakhir</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="text-xs text-slate-500"><tr class="border-b"><th class="py-2 font-medium">Waktu</th><th class="font-medium">Jenis</th><th class="text-right font-medium">Perubahan</th><th class="text-right font-medium">Sebelum → Sesudah</th><th class="font-medium">Keterangan</th></tr></thead>
                <tbody class="divide-y">
                @forelse ($riwayat as $r)
                    <tr>
                        <td class="whitespace-nowrap py-2">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $tipe[$r->tipe] ?? $r->tipe }}</td>
                        <td class="text-right font-semibold {{ $r->qty_perubahan < 0 ? 'text-red-600' : 'text-sukses' }}">{{ $r->qty_perubahan > 0 ? '+' : '' }}{{ $r->qty_perubahan }}</td>
                        <td class="text-right">{{ $r->stok_sebelum }} → {{ $r->stok_sesudah }}</td>
                        <td class="text-xs text-slate-500">{{ $r->referensi ?? $r->catatan }}@if ($r->referensi && $r->catatan) · {{ $r->catatan }} @endif @if ($r->pengurus) <span class="text-slate-400">({{ $r->pengurus->nama_pengurus }})</span> @endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
