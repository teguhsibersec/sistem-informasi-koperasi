@extends('layouts.admin')
@section('judul', 'Pembayaran')

@section('isi')
@php $rp = fn ($n) => \App\Support\Rupiah::format($n); @endphp
<h1 class="text-2xl font-bold text-navy">Pembayaran</h1>

<div class="mt-4 flex flex-wrap gap-2">
    @foreach (['menunggu' => 'Menunggu', 'berhasil' => 'Berhasil', 'gagal' => 'Gagal', 'kedaluwarsa' => 'Kedaluwarsa'] as $k => $v)
        <a href="{{ route('admin.pembayaran.index', ['status' => $k]) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ $status === $k ? 'bg-navy text-white' : 'border bg-white text-slate-600' }}">{{ $v }}</a>
    @endforeach
</div>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                <th class="px-4 py-3 font-medium">No. Nota</th><th class="font-medium">Pembeli</th><th class="font-medium">Metode</th>
                <th class="font-medium">Tagihan</th><th class="font-medium">Status</th><th class="font-medium">Batas / Dibayar</th><th class="pr-4 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @forelse ($pembayaran as $b)
            <tr>
                <td class="px-4 py-3"><a href="{{ route('admin.transaksi.show', $b->id_penjualan) }}" class="font-mono text-xs text-navy hover:underline">{{ $b->penjualan->no_nota_penjualan }}</a></td>
                <td>{{ $b->penjualan->anggota?->nama_anggota ?? 'Umum' }}</td>
                <td>{{ strtoupper($b->metode_pembayaran) }}</td>
                <td class="whitespace-nowrap">{{ $rp($b->jumlah_tagihan) }}</td>
                <td><x-status-pembayaran :status="$b->status_pembayaran" /></td>
                <td class="whitespace-nowrap text-xs">
                    {{ ($b->dibayar_pada ?? $b->kedaluwarsa_pada)?->format('d/m H:i') ?? '-' }}
                    @if ($b->bukti_bayar)
                        <a href="{{ asset('storage/' . $b->bukti_bayar) }}" target="_blank" rel="noopener" class="ml-1 text-navy underline">bukti</a>
                    @endif
                </td>
                <td class="py-2 pr-4 text-right">
                    @if ($b->status_pembayaran === 'menunggu' && $b->metode_pembayaran === 'qris')
                        <form method="POST" action="{{ route('admin.pembayaran.lunas', $b) }}" onsubmit="return confirm('Pastikan dana sudah masuk. Tandai lunas?')">
                            @csrf <button class="btn-kuning px-3 py-1.5 text-xs">Tandai Lunas</button>
                        </form>
                    @elseif ($b->status_pembayaran === 'menunggu' && $b->metode_pembayaran === 'cash')
                        <form method="POST" action="{{ route('admin.pembayaran.cash', $b) }}" class="flex justify-end gap-1">
                            @csrf
                            <input type="number" name="uang_diterima" min="{{ (int) $b->jumlah_tagihan }}" value="{{ (int) $b->jumlah_tagihan }}" class="input w-28 py-1.5 text-xs" aria-label="Uang diterima">
                            <button class="btn-navy px-3 py-1.5 text-xs">Konfirmasi</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pembayaran->links() }}</div>
@endsection
