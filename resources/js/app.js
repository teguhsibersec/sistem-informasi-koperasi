import Alpine from 'alpinejs';
import './admin';
import './pos';
import './gudang';

window.Alpine = Alpine;
Alpine.start();

// Pencarian produk AJAX di toko. Di halaman belanja hanya memperbarui
// #hasil-cari; di halaman lain dialihkan ke halaman belanja.
window.cariProduk = (() => {
    let timer;
    return async (q) => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const form = document.getElementById('form-cari');
            if (!form) return;

            const el = document.getElementById('hasil-cari');
            if (!el) {
                // Bukan halaman belanja: langsung ke halaman belanja dengan q
                const url = new URL(form.action);
                url.searchParams.set('q', q);
                window.location.href = url.toString();
                return;
            }
            const params = new URLSearchParams(window.location.search);
            params.set('q', q);
            params.delete('page');
            const url = new URL(form.action);
            url.search = params.toString();
            try {
                const res = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) el.innerHTML = await res.text();
            } catch (e) {
                // diam, hasil tetap yang lama
            }
        }, 300);
    };
})();
