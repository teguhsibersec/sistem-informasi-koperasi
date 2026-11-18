@php
    $rp = fn ($n) => number_format((float) $n, 0, ',', '.');
    $metode = ['cash' => 'CASH', 'qris' => 'QRIS'][$pembayaran?->metode_pembayaran] ?? '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota {{ $penjualan->no_nota_penjualan }}</title>
    <style>
        @page { size: 58mm auto; margin: 2mm; }
        body { font-family: 'DejaVu Sans Mono', monospace; font-size: 9px; color: #000; margin: 0; }
        @media screen { body { width: 54mm; margin: 12px auto; } }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 1px 0; }
        .c { text-align: center; } .r { text-align: right; } .b { font-weight: bold; }
        .garis { border-top: 1px dashed #000; margin: 4px 0; }
        .no-print { text-align: center; margin-top: 12px; font-family: sans-serif; }
        .no-print a, .no-print button { font-size: 12px; margin: 2px; padding: 6px 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="c b" style="font-size:11px">{{ $koperasi }}</div>
    <div class="c">{{ $alamat }}</div>
    <div class="garis"></div>
    <table>
        <tr><td>Nota</td><td class="r">{{ $penjualan->no_nota_penjualan }}</td></tr>
        <tr><td>Tanggal</td><td class="r">{{ $penjualan->tgl_penjualan->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Kasir</td><td class="r">{{ $penjualan->pengurus?->nama_pengurus ?? '-' }}</td></tr>
        <tr><td>Pembeli</td><td class="r">{{ $penjualan->anggota?->nama_anggota ?? 'Umum' }}</td></tr>
    </table>
    <div class="garis"></div>
    <table>
        @foreach ($penjualan->details as $d)
            <tr><td colspan="2">{{ $d->barang->nama_barang }}</td></tr>
            <tr>
                <td>{{ $d->qty_detail_penjualan }} x {{ $rp($d->harga_satuan_detail_penjualan) }}</td>
                <td class="r">{{ $rp($d->subtotal) }}</td>
            </tr>
        @endforeach
    </table>
    <div class="garis"></div>
    <table>
        <tr class="b"><td>TOTAL</td><td class="r">{{ $rp($penjualan->total) }}</td></tr>
        <tr><td>Metode</td><td class="r">{{ $metode }}</td></tr>
        @if ($pembayaran?->metode_pembayaran === 'cash' && $pembayaran->status_pembayaran === 'berhasil')
            <tr><td>Tunai</td><td class="r">{{ $rp($pembayaran->uang_diterima) }}</td></tr>
            <tr><td>Kembali</td><td class="r">{{ $rp($pembayaran->kembalian) }}</td></tr>
        @endif
        <tr><td>Status</td><td class="r">{{ $pembayaran?->status_pembayaran === 'berhasil' ? 'LUNAS' : strtoupper(str_replace('_', ' ', $penjualan->status_penjualan)) }}</td></tr>
    </table>
    <div class="garis"></div>
    <div class="c">Terima kasih telah berbelanja</div>
    <div class="c">di {{ config('koperasi.nama') }}</div>

    @unless ($pdf)
        @php $mhs = auth()->check() && auth()->user()->isAnggota(); @endphp
        <div class="no-print">
            <button onclick="window.print()">Cetak</button>
            <a href="{{ $mhs ? route('toko.pesanan.nota.pdf', $penjualan) : route('nota.pdf', $penjualan) }}">PDF</a>
            @if ($mhs)
                <a href="{{ route('toko.pesanan.show', $penjualan) }}">Kembali</a>
            @else
                <a href="{{ route('kasir.pos') }}">Transaksi Baru</a>
            @endif
        </div>
        @if (request()->boolean('cetak'))
            <script>window.addEventListener('load', () => setTimeout(() => window.print(), 300));</script>
        @endif
    @endunless
</body>
</html>
