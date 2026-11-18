const rp = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

document.addEventListener('alpine:init', () => {
    // Baris barang dinamis pada form pembelian.
    Alpine.data('formPembelian', (barang, awal) => ({
        barang,
        baris: awal && awal.length ? awal.map((b) => ({ id_barang: b.id_barang ?? '', qty: b.qty ?? 1, harga: b.harga ?? '' })) : [{ id_barang: '', qty: 1, harga: '' }],
        rp,
        tambah() { this.baris.push({ id_barang: '', qty: 1, harga: '' }); },
        hapus(i) { if (this.baris.length > 1) this.baris.splice(i, 1); },
        get total() { return this.baris.reduce((s, b) => s + (Number(b.qty) || 0) * (Number(b.harga) || 0), 0); },
    }));
});
