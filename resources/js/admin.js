import Chart from 'chart.js/auto';

const rp = (v) => new Intl.NumberFormat('id-ID').format(v);

document.addEventListener('alpine:init', () => {
    // Tanggal dan jam WIB real-time di header.
    Alpine.data('jamWib', () => ({
        tgl: '',
        jam: '',
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            const n = new Date();
            this.tgl = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
            }).format(n);
            this.jam = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false,
            }).format(n).replace('.', ':') + ' WIB';
        },
    }));

    // Grafik penjualan. Instance Chart disimpan di closure (bukan data reaktif Alpine).
    Alpine.data('grafikPenjualan', (url, awal) => {
        let chart = null;

        return {
            rentang: '7',
            judul: awal.judul,
            memuat: false,
            init() {
                this.gambar(awal);
            },
            async ganti() {
                this.memuat = true;
                try {
                    const res = await fetch(`${url}?rentang=${this.rentang}`, { headers: { Accept: 'application/json' } });
                    if (res.ok) {
                        this.gambar(await res.json());
                    }
                } finally {
                    this.memuat = false;
                }
            },
            gambar(d) {
                this.judul = d.judul;
                if (chart) {
                    chart.data.labels = d.labels;
                    chart.data.datasets[0].data = d.data;
                    chart.update();
                    return;
                }
                const ctx = this.$refs.kanvas.getContext('2d');
                const g = ctx.createLinearGradient(0, 0, 0, 260);
                g.addColorStop(0, 'rgba(22,163,74,.25)');
                g.addColorStop(1, 'rgba(22,163,74,0)');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: d.labels,
                        datasets: [{ data: d.data, borderColor: '#16A34A', backgroundColor: g, fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: '#16A34A' }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => 'Rp ' + rp(c.parsed.y) } } },
                        scales: { y: { beginAtZero: true, ticks: { callback: (v) => rp(v) } }, x: { grid: { display: false } } },
                    },
                });
            },
        };
    });
});
