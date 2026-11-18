@props(['status'])
@php
    $peta = [
        'menunggu' => ['Menunggu', 'bg-orange-100 text-orange-700'],
        'berhasil' => ['Berhasil', 'bg-green-100 text-green-700'],
        'gagal' => ['Gagal', 'bg-red-100 text-red-700'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-slate-200 text-slate-600'],
    ];
    [$teks, $kelas] = $peta[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-block whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $kelas }}">{{ $teks }}</span>
