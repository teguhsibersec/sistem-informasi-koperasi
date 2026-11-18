import QRCode from 'qrcode';

const rp = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
const json = { Accept: 'application/json' };

document.addEventListener('alpine:init', () => {
    // ---------- Kasir (POS) ----------
    Alpine.data('posKasir', (cfg) => ({
        q: '', hasil: [], keranjang: [], metode: 'cash', uang: '',
        qa: '', anggotaHasil: [], anggota: null, galat: '', proses: false,
        cepatNominal: [10000, 20000, 50000, 100000],
        rp,

        init() { this.cari(); },

        async cari() {
            const r = await fetch(`${cfg.urlBarang}?q=${encodeURIComponent(this.q)}`, { headers: json });
            if (r.ok) this.hasil = await r.json();
        },
        // Scanner barcode mengetik kode lalu Enter: kode persis (atau hasil tunggal) langsung masuk keranjang.
        async enterCari() {
            const q = this.q.trim().toLowerCase();
            if (!q) return;
            await this.cari();
            const cocok = this.hasil.find((b) => b.kode_barang.toLowerCase() === q) || (this.hasil.length === 1 ? this.hasil[0] : null);
            if (cocok) { this.tambah(cocok); this.q = ''; this.cari(); }
        },
        tambah(b) {
            this.galat = '';
            const it = this.keranjang.find((i) => i.id_barang === b.id_barang);
            if (it) {
                if (it.qty < b.stok) it.qty++;
                else this.galat = `Stok ${b.nama_barang} hanya ${b.stok}.`;
            } else if (b.stok > 0) {
                this.keranjang.push({ id_barang: b.id_barang, nama: b.nama_barang, harga: b.harga, stok: b.stok, qty: 1 });
            }
        },
        ubah(it, d) {
            const n = it.qty + d;
            if (n < 1) return this.hapus(it);
            if (n > it.stok) { this.galat = `Stok ${it.nama} hanya ${it.stok}.`; return; }
            it.qty = n;
        },
        setQty(it) { it.qty = Math.max(1, Math.min(it.stok, parseInt(it.qty) || 1)); },
        hapus(it) { this.keranjang = this.keranjang.filter((i) => i !== it); },

        get total() { return this.keranjang.reduce((s, i) => s + i.qty * i.harga, 0); },
        get kembalian() { return Math.max(0, (Number(this.uang) || 0) - this.total); },
        get bisaProses() {
            return this.keranjang.length > 0 && !this.proses && (this.metode === 'qris' || (Number(this.uang) || 0) >= this.total);
        },

        async cariAnggota() {
            if (this.qa.trim().length < 2) { this.anggotaHasil = []; return; }
            const r = await fetch(`${cfg.urlAnggota}?q=${encodeURIComponent(this.qa)}`, { headers: json });
            if (r.ok) this.anggotaHasil = await r.json();
        },
        pilihAnggota(a) { this.anggota = a; this.qa = ''; this.anggotaHasil = []; },

        async bayar() {
            this.proses = true;
            this.galat = '';
            try {
                const r = await fetch(cfg.urlCheckout, {
                    method: 'POST',
                    headers: { ...json, 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({
                        items: this.keranjang.map((i) => ({ id_barang: i.id_barang, qty: i.qty })),
                        metode: this.metode,
                        id_anggota: this.anggota ? this.anggota.id_anggota : null,
                        uang_diterima: this.metode === 'cash' ? Number(this.uang) : null,
                    }),
                });
                const d = await r.json();
                if (!r.ok) { this.galat = d.message || 'Gagal memproses transaksi.'; this.cari(); return; }
                window.location = d.redirect;
            } catch (e) {
                this.galat = 'Koneksi bermasalah. Coba lagi.';
            } finally {
                this.proses = false;
            }
        },
    }));

    // ---------- Halaman QRIS: QR + countdown + polling ----------
    Alpine.data('qrisBayar', (cfg) => ({
        sisa: cfg.sisa, status: cfg.status, rp,

        init() {
            if (this.$refs.qr && cfg.payload) QRCode.toCanvas(this.$refs.qr, cfg.payload, { width: 260, margin: 1 });
            setInterval(() => { if (this.sisa > 0) this.sisa--; }, 1000);
            setInterval(() => this.cek(), 4000);
        },
        get waktu() {
            const m = String(Math.floor(this.sisa / 60)).padStart(2, '0');
            const s = String(this.sisa % 60).padStart(2, '0');
            return `${m}:${s}`;
        },
        async cek() {
            if (this.status !== 'menunggu') return;
            try {
                const r = await fetch(cfg.urlStatus, { headers: json });
                if (!r.ok) return;
                const d = await r.json();
                this.status = d.status;
                this.sisa = d.sisa_detik;
                if (d.status === 'berhasil') window.location = cfg.urlNota;
            } catch (e) { /* abaikan, coba lagi pada polling berikutnya */ }
        },
    }));
});
