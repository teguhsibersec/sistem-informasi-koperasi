@props(['status'])
@php
    $peta = [
        'selesai' => ['Selesai', 'bg-green-100 text-green-700'],
        'diproses' => ['Diproses', 'bg-yellow-100 text-yellow-700'],
        'menunggu_bayar' => ['Menunggu Bayar', 'bg-orange-100 text-orange-700'],
        'siap_diambil' => ['Siap Diambil', 'bg-blue-100 text-blue-700'],
        'batal' => ['Batal', 'bg-red-100 text-red-700'],
    ];
    [$teks, $kelas] = $peta[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-block whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $kelas }}">{{ $teks }}</span>
