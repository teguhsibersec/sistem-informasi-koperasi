@extends('layouts.admin')
@section('judul', 'Detail Pembelian')

@section('isi')
@php $rp = fn ($n) => \App\Support\Rupiah::format($n); $diterima = $pembelian->status_pembelian === 'diterima'; @endphp
<a href="{{ route('admin.pembelian.index') }}" class="text-xs text-navy hover:underline">← Semua pembelian</a>
<div class="mt-1 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-navy">Faktur {{ $pembelian->no_faktur_pembelian }}</h1>
    {!! $diterima ? '<span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700">Diterima</span>' : '<span class="rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-700">Dipesan</span>' !!}
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
        <table class="w-full text-left text-sm">
            <thead class="text-xs text-slate-500"><tr class="border-b"><th class="py-2 font-medium">Barang</th><th class="text-right font-medium">Qty</th><th class="text-right font-medium">Harga Beli</th><th class="text-right font-medium">Subtotal</th></tr></thead>
            <tbody class="divide-y">
            @foreach ($pembelian->details as $d)
                <tr><td class="py-2">{{ $d->barang->nama_barang }} <span class="font-mono text-xs text-slate-400">{{ $d->barang->kode_barang }}</span></td><td class="text-right">{{ $d->qty_detail_pembelian }}</td><td class="text-right">{{ $rp($d->harga_beli_detail_pembelian) }}</td><td class="text-right">{{ $rp($d->qty_detail_pembelian * $d->harga_beli_detail_pembelian) }}</td></tr>
            @endforeach
            </tbody>
            <tfoot><tr class="border-t font-bold"><td colspan="3" class="py-2">Total</td><td class="text-right">{{ $rp($pembelian->total) }}</td></tr></tfoot>
        </table>
    </section>

    <aside class="h-fit space-y-3 rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm">
        <div class="flex justify-between"><span class="text-slate-500">Pemasok</span><span>{{ $pembelian->pemasok?->nama_pemasok }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Tanggal</span><span>{{ $pembelian->tgl_pembelian->format('d/m/Y') }}</span></div>
        @unless ($diterima)
            <hr>
            <form method="POST" action="{{ route('admin.pembelian.terima', $pembelian) }}" onsubmit="return confirm('Barang sudah tiba? Stok akan bertambah sesuai faktur.')">
                @csrf <button class="btn-kuning w-full">Terima Barang</button>
            </form>
            <form method="POST" action="{{ route('admin.pembelian.destroy', $pembelian) }}" onsubmit="return confirm('Hapus pembelian ini?')">
                @csrf @method('DELETE') <button class="w-full rounded-lg border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus Pembelian</button>
            </form>
        @else
            <p class="rounded-lg bg-green-50 p-3 text-xs text-green-700">Barang sudah diterima dan stok sudah ditambahkan. Lihat di <a href="{{ route('admin.stok.riwayat', ['q' => $pembelian->no_faktur_pembelian]) }}" class="underline">riwayat stok</a>.</p>
        @endunless
    </aside>
</div>
@endsection
