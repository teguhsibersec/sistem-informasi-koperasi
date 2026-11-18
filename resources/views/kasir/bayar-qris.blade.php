@extends('layouts.admin')
@section('judul', 'Pembayaran QRIS')

@section('isi')
@php
    $cfg = [
        'payload' => $pembayaran->qris_payload,
        'sisa' => $sisa,
        'status' => $pembayaran->status_pembayaran,
        'urlStatus' => route('kasir.bayar.status', $penjualan),
        'urlNota' => route('nota.show', [$penjualan, 'cetak' => 1]),
    ];
@endphp

<div class="mx-auto max-w-md rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm" x-data="qrisBayar(@js($cfg))">
    <p class="text-xs text-slate-500">No. Nota {{ $penjualan->no_nota_penjualan }}</p>
    <p class="mt-1 text-sm text-slate-600">Total yang harus dibayar</p>
    <p class="text-3xl font-bold text-navy">{{ \App\Support\Rupiah::format($pembayaran->jumlah_tagihan) }}</p>

    <div x-show="status === 'menunggu'" class="mt-4">
        @if (\App\Models\Pengaturan::ambil('qris_gambar') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\Pengaturan::ambil('qris_gambar')))
                <img src="{{ \Illuminate\Support\Facades\Storage::url(\App\Models\Pengaturan::ambil('qris_gambar')) }}" alt="QRIS {{ \App\Models\Pengaturan::ambil('nama_merchant_qris', config('koperasi.nama')) }}" class="mx-auto max-h-72 w-auto object-contain">
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ \App\Models\Pengaturan::ambil('nama_merchant_qris', config('koperasi.nama')) }}</p>
                <p class="mt-1 text-xs text-amber-700">Masukkan nominal pembayaran sesuai total tagihan jika aplikasi pembayaran memintanya.</p>
            @else
                <canvas x-ref="qr" class="mx-auto"></canvas>
            @endif
        <p class="mt-3 text-sm text-slate-600">Berlaku <span class="font-mono text-lg font-bold text-red-600" x-text="waktu"></span></p>
        <p class="mt-1 text-xs text-slate-400">Status dicek otomatis tiap beberapa detik.</p>
    </div>

    <div x-show="status !== 'menunggu'" x-cloak class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-600">
        <p class="font-semibold">QRIS tidak berlaku lagi.</p>
        <p>Pesanan dibatalkan dan stok sudah dikembalikan.</p>
        <a href="{{ route('kasir.pos') }}" class="btn-navy mt-3">Kembali ke Kasir</a>
    </div>

    <div x-show="status === 'menunggu'" class="mt-5 grid gap-2">
        <form method="POST" action="{{ route('kasir.bayar.lunas', $penjualan) }}" onsubmit="return confirm('Pastikan dana sudah masuk. Tandai lunas?')">
            @csrf
            <button class="btn-kuning w-full">Tandai Lunas</button>
        </form>
        <form method="POST" action="{{ route('kasir.bayar.batal', $penjualan) }}" onsubmit="return confirm('Batalkan transaksi ini?')">
            @csrf
            <button class="w-full rounded-lg border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">Batalkan Transaksi</button>
        </form>
    </div>
</div>
@endsection
