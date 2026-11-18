@extends('layouts.admin')
@section('judul', 'Pembelian / Pemasok')

@section('isi')
@php $rp = fn ($n) => \App\Support\Rupiah::format($n); @endphp
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-navy">Pembelian</h1>
        <nav class="mt-1 flex gap-4 text-sm"><span class="font-semibold text-navy">Pembelian</span><a href="{{ route('admin.pembelian.pemasok.index') }}" class="text-slate-500 hover:text-navy">Pemasok</a></nav>
    </div>
    <a href="{{ route('admin.pembelian.create') }}" class="btn-kuning">+ Catat Pembelian</a>
</div>

<form method="GET" class="mt-4 flex flex-wrap gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <select name="status" class="input max-w-[180px]"><option value="">Semua status</option><option value="dipesan" @selected(request('status') === 'dipesan')>Dipesan</option><option value="diterima" @selected(request('status') === 'diterima')>Diterima</option></select>
    <select name="pemasok" class="input max-w-xs"><option value="">Semua pemasok</option>@foreach ($pemasok as $p) <option value="{{ $p->id_pemasok }}" @selected((string) request('pemasok') === (string) $p->id_pemasok)>{{ $p->nama_pemasok }}</option> @endforeach</select>
    <button class="btn-navy">Filter</button>
</form>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="px-4 py-3 font-medium">No. Faktur</th><th class="font-medium">Tanggal</th><th class="font-medium">Pemasok</th><th class="text-right font-medium">Item</th><th class="text-right font-medium">Total</th><th class="font-medium">Status</th><th></th></tr></thead>
        <tbody class="divide-y">
        @forelse ($pembelian as $p)
            <tr>
                <td class="px-4 py-3 font-mono text-xs">{{ $p->no_faktur_pembelian }}</td>
                <td class="whitespace-nowrap">{{ $p->tgl_pembelian->format('d/m/Y') }}</td>
                <td>{{ $p->pemasok?->nama_pemasok }}</td>
                <td class="text-right">{{ $p->details->count() }}</td>
                <td class="whitespace-nowrap text-right">{{ $rp($p->total) }}</td>
                <td>{!! $p->status_pembelian === 'diterima' ? '<span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Diterima</span>' : '<span class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">Dipesan</span>' !!}</td>
                <td class="pr-4 text-right"><a href="{{ route('admin.pembelian.show', $p) }}" class="text-xs font-medium text-navy hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-10 text-center text-slate-400">Belum ada pembelian.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pembelian->links() }}</div>
@endsection
