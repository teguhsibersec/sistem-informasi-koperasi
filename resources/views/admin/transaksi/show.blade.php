@extends('layouts.admin')
@section('judul', 'Detail Transaksi')

@section('isi')
@php
    $rp = fn ($n) => \App\Support\Rupiah::format($n);
    $bayarTerakhir = $penjualan->pembayaran->sortByDesc('id_pembayaran')->first();
@endphp
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <a href="{{ route('admin.transaksi.index') }}" class="text-xs text-navy hover:underline">← Semua transaksi</a>
        <h1 class="text-2xl font-bold text-navy">Nota {{ $penjualan->no_nota_penjualan }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('nota.show', $penjualan) }}" class="btn-navy">Lihat Nota</a>
        <a href="{{ route('nota.pdf', $penjualan) }}" class="btn border border-slate-300 bg-white text-slate-700">PDF</a>
        @if ($penjualan->status_penjualan === 'menunggu_bayar' && $bayarTerakhir?->metode_pembayaran === 'qris')
            <a href="{{ route('kasir.bayar', $penjualan) }}" class="btn-kuning">Buka QRIS</a>
        @endif
    </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
        <table class="w-full text-left text-sm">
            <thead class="text-xs text-slate-500"><tr class="border-b"><th class="py-2 font-medium">Barang</th><th class="font-medium">Qty</th><th class="font-medium">Harga</th><th class="text-right font-medium">Subtotal</th></tr></thead>
            <tbody class="divide-y">
            @foreach ($penjualan->details as $d)
                <tr><td class="py-2">{{ $d->barang->nama_barang }}</td><td>{{ $d->qty_detail_penjualan }}</td><td>{{ $rp($d->harga_satuan_detail_penjualan) }}</td><td class="text-right">{{ $rp($d->subtotal) }}</td></tr>
            @endforeach
            </tbody>
            <tfoot><tr class="border-t font-bold"><td colspan="3" class="py-2">Total</td><td class="text-right">{{ $rp($penjualan->total) }}</td></tr></tfoot>
        </table>
    </section>

    <section class="space-y-3 rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm">
        <div class="flex justify-between"><span class="text-slate-500">Status</span><x-status-penjualan :status="$penjualan->status_penjualan" /></div>
        <div class="flex justify-between"><span class="text-slate-500">Waktu</span><span>{{ $penjualan->tgl_penjualan->format('d/m/Y H:i') }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Sumber</span><span>{{ ucfirst($penjualan->sumber_penjualan) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Pembeli</span><span>{{ $penjualan->anggota?->nama_anggota ?? 'Umum' }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Pengurus</span><span>{{ $penjualan->pengurus?->nama_pengurus ?? '-' }}</span></div>
        @if ($bayarTerakhir)
            <hr>
            <div class="flex justify-between"><span class="text-slate-500">Metode</span><span>{{ strtoupper($bayarTerakhir->metode_pembayaran) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Pembayaran</span><x-status-pembayaran :status="$bayarTerakhir->status_pembayaran" /></div>
            @if ($bayarTerakhir->metode_pembayaran === 'cash' && $bayarTerakhir->status_pembayaran === 'berhasil')
                <div class="flex justify-between"><span class="text-slate-500">Uang diterima</span><span>{{ $rp($bayarTerakhir->uang_diterima) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Kembalian</span><span>{{ $rp($bayarTerakhir->kembalian) }}</span></div>
            @endif
        @endif
        @if ($penjualan->catatan_penjualan)
            <hr><p class="text-xs text-slate-500">Catatan: {{ $penjualan->catatan_penjualan }}</p>
        @endif

        @if ($penjualan->sumber_penjualan === 'online' && in_array($penjualan->status_penjualan, ['diproses', 'siap_diambil']))
            <hr>
            <div class="flex gap-2">
                @if ($penjualan->status_penjualan === 'diproses')
                    <form method="POST" action="{{ route('admin.transaksi.status', $penjualan) }}" class="flex-1">@csrf <input type="hidden" name="status" value="siap_diambil"><button class="btn-navy w-full text-xs">Tandai Siap Diambil</button></form>
                @endif
                <form method="POST" action="{{ route('admin.transaksi.status', $penjualan) }}" class="flex-1">@csrf <input type="hidden" name="status" value="selesai"><button class="btn-kuning w-full text-xs">Selesaikan</button></form>
            </div>
        @endif

        @if ($penjualan->status_penjualan !== 'batal')
            <hr>
            <form method="POST" action="{{ route('admin.transaksi.batal', $penjualan) }}" class="space-y-2" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                @csrf
                <label class="label" for="alasan">Batalkan transaksi</label>
                <input id="alasan" name="alasan" required maxlength="200" placeholder="Alasan pembatalan" class="input">
                <button class="w-full rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Batalkan & Kembalikan Stok</button>
            </form>
        @endif
    </section>
</div>
@endsection
