@extends('layouts.admin')
@section('judul', 'Laporan')

@section('isi')
@php
    $jenis = $laporan['jenis'];
    $stok = $jenis === 'stok';
    $rentang = ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()];
    $param = ['jenis' => $jenis] + ($stok ? [] : $rentang)
        + ($jenis === 'terlaris' ? ['top' => $opsi['top']] : [])
        + ($stok && $opsi['hanya_menipis'] ? ['hanya_menipis' => 1] : []);
    $preset = [
        'Hari Ini' => [now(), now()],
        '7 Hari' => [now()->subDays(6), now()],
        'Bulan Ini' => [now()->startOfMonth(), now()],
        'Bulan Lalu' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
        'Tahun Ini' => [now()->startOfYear(), now()],
    ];
@endphp

<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-navy">Laporan</h1>
        <p class="text-sm text-slate-500">{{ $laporan['periode'] }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.laporan.excel', $param) }}" class="btn bg-sukses text-white hover:opacity-90">Export Excel</a>
        <a href="{{ route('admin.laporan.pdf', $param) }}" target="_blank" class="btn-navy">Export PDF</a>
    </div>
</div>

<nav class="mt-4 flex gap-1 overflow-x-auto rounded-xl bg-white p-2 shadow-sm">
    @foreach ($daftarJenis as $k => $label)
        <a href="{{ route('admin.laporan.index', ['jenis' => $k] + ($k === 'stok' ? [] : $rentang)) }}"
           class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium {{ $jenis === $k ? 'bg-navy text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
    @endforeach
</nav>

<form method="GET" action="{{ route('admin.laporan.index') }}" class="mt-3 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <input type="hidden" name="jenis" value="{{ $jenis }}">
    @unless ($stok)
        <div><label class="label" for="dari">Dari</label><input id="dari" type="date" name="dari" value="{{ $dari->toDateString() }}" class="input"></div>
        <div><label class="label" for="sampai">Sampai</label><input id="sampai" type="date" name="sampai" value="{{ $sampai->toDateString() }}" class="input"></div>
        <div class="flex flex-wrap gap-1">
            @foreach ($preset as $nama => [$a, $b])
                <a href="{{ route('admin.laporan.index', ['jenis' => $jenis, 'dari' => $a->toDateString(), 'sampai' => $b->toDateString()] + ($jenis === 'terlaris' ? ['top' => $opsi['top']] : [])) }}"
                   class="rounded-full border px-3 py-1.5 text-xs text-slate-600 hover:border-navy">{{ $nama }}</a>
            @endforeach
        </div>
    @endunless
    @if ($jenis === 'terlaris')
        <div><label class="label" for="top">Tampilkan</label>
            <select id="top" name="top" class="input">
                @foreach ([10, 20, 50, 100] as $n) <option value="{{ $n }}" @selected($opsi['top'] === $n)>{{ $n }} teratas</option> @endforeach
            </select>
        </div>
    @endif
    @if ($stok)
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="hanya_menipis" value="1" @checked($opsi['hanya_menipis']) class="rounded border-slate-300"> Hanya stok menipis</label>
    @endif
    <button class="btn-navy">Terapkan</button>
</form>

<div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($laporan['ringkasan'] as $r)
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">{{ $r['label'] }}</p>
            <p class="mt-1 text-xl font-bold text-navy">{{ \App\Support\FormatLaporan::sel($r['nilai'], $r['tipe']) }}</p>
        </div>
    @endforeach
</div>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full min-w-[640px] text-sm">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                @foreach ($laporan['kolom'] as $k)
                    <th class="px-4 py-3 font-medium {{ $k['tipe'] === 'teks' ? 'text-left' : 'text-right' }}">{{ $k['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($laporan['baris'] as $baris)
                <tr>
                    @foreach ($baris as $i => $nilai)
                        <td class="whitespace-nowrap px-4 py-2.5 {{ $laporan['kolom'][$i]['tipe'] === 'teks' ? '' : 'text-right' }}">{{ \App\Support\FormatLaporan::sel($nilai, $laporan['kolom'][$i]['tipe']) }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($laporan['kolom']) }}" class="py-10 text-center text-slate-400">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if ($laporan['total'] && count($laporan['baris']))
            <tfoot class="border-t-2 bg-slate-50 font-semibold">
                <tr>
                    @foreach ($laporan['total'] as $i => $nilai)
                        <td class="whitespace-nowrap px-4 py-3 {{ $laporan['kolom'][$i]['tipe'] === 'teks' ? '' : 'text-right' }}">{{ \App\Support\FormatLaporan::sel($nilai, $laporan['kolom'][$i]['tipe']) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<ul class="mt-3 list-disc space-y-1 pl-5 text-xs text-slate-500">
    @foreach ($laporan['catatan'] as $c) <li>{{ $c }}</li> @endforeach
</ul>
@endsection
