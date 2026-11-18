@props(['nama'])
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => 'h-5 w-5']) }}>
@switch($nama)
    @case('dashboard')
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        @break
    @case('produk')
        <path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/>
        @break
    @case('kategori')
        <path d="M9 6h11M9 12h11M9 18h11"/><circle cx="4.5" cy="6" r="1"/><circle cx="4.5" cy="12" r="1"/><circle cx="4.5" cy="18" r="1"/>
        @break
    @case('stok')
        <rect x="3" y="3" width="18" height="7" rx="1"/><rect x="3" y="14" width="18" height="7" rx="1"/><path d="M7 6.5h4M7 17.5h4"/>
        @break
    @case('transaksi')
        <path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2z"/><path d="M9 7h6M9 11h6"/>
        @break
    @case('anggota')
        <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>
        @break
    @case('laporan')
        <path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/>
        @break
    @case('pengaturan')
        <circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9L7 7M17 17l2.1 2.1M4.9 19.1L7 17M17 7l2.1-2.1"/>
        @break
    @case('kasir')
        <rect x="4" y="3" width="16" height="6" rx="1"/><path d="M6 9l-2 12h16L18 9M9 14h6"/>
        @break
    @case('pembelian')
    @case('keranjang')
        <path d="M3 4h2l2.5 11h10L20 7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>
        @break
    @case('pembayaran')
        <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>
        @break
    @case('qr')
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM18 18h3v3h-3zM14 20h2M19 14v2"/>
        @break
    @case('tunai')
        <rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 12h.01M18 12h.01"/>
        @break
    @case('uang')
        <circle cx="12" cy="12" r="9"/><path d="M12 6v12M9.5 9.5c0-1 1-1.5 2.5-1.5s2.5.5 2.5 1.5-1 1.5-2.5 1.5-2.5.5-2.5 1.5 1 1.5 2.5 1.5 2.5-.5 2.5-1.5"/>
        @break
    @case('peringatan')
        <path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18h.01"/>
        @break
    @case('panah')
        <path d="M9 6l6 6-6 6"/>
        @break
    @case('menu')
        <path d="M4 6h16M4 12h16M4 18h16"/>
        @break
    @case('turun')
        <path d="M6 9l6 6 6-6"/>
        @break
    @case('notebook')
        <path d="M6 3h12a1 1 0 011 1v16a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 7h6M9 11h6M3 8h2M3 12h2M3 16h2"/>
        @break
    @case('cup')
        <path d="M6 8h12l-1.2 12H7.2L6 8z"/><path d="M5 8h14M9 3l1 5M15 3l-1 5"/>
        @break
    @case('bag')
        <path d="M5 8h14l1 13H4L5 8z"/><path d="M9 8V6a3 3 0 016 0v2"/>
        @break
    @case('shirt')
        <path d="M8 3L3 6l2 4 3-1v12h8V9l3 1 2-4-5-3a4 4 0 01-8 0z"/>
        @break
    @case('grid')
        <rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/>
        @break
    @case('cari')
        <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
        @break
    @case('truk')
        <path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/>
        @break
    @case('perisai')
        <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>
        @break
    @case('headset')
        <path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="3" y="14" width="4" height="6" rx="1"/><rect x="17" y="14" width="4" height="6" rx="1"/>
        @break
    @case('komunitas')
        <circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6M15 14.5c3 0 6 1.5 6 5"/>
        @break
    @case('centang')
        <path d="M5 12l5 5L20 7"/>
        @break
@endswitch
</svg>
