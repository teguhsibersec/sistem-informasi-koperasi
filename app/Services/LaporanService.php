<?php

namespace App\Services;

use App\Models\Barang;
use App\Support\Tanggal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Semua laporan memakai satu bentuk keluaran supaya tampilan HTML, PDF, dan Excel berbagi data:
 *   jenis, judul, periode, kolom[{label,tipe}], baris[][], total[]|null, ringkasan[{label,nilai,tipe}], catatan[]
 * tipe kolom: teks | angka | rupiah | persen.
 * Dasar angka penjualan: transaksi berstatus 'selesai' (sama dengan dashboard).
 */
class LaporanService
{
    public const JENIS = [
        'penjualan-harian' => 'Penjualan Harian',
        'penjualan-bulanan' => 'Penjualan Bulanan',
        'metode' => 'Per Metode Bayar',
        'terlaris' => 'Barang Terlaris',
        'laba' => 'Laba Kotor',
        'stok' => 'Stok',
    ];

    private const BARIS = 'd.qty_detail_penjualan * d.harga_satuan_detail_penjualan';

    public function buat(string $jenis, Carbon $dari, Carbon $sampai, array $opsi = []): array
    {
        $dari = $dari->copy()->startOfDay();
        $sampai = $sampai->copy()->endOfDay();

        $laporan = match ($jenis) {
            'penjualan-harian' => $this->penjualan($dari, $sampai, false),
            'penjualan-bulanan' => $this->penjualan($dari, $sampai, true),
            'metode' => $this->metode($dari, $sampai),
            'terlaris' => $this->terlaris($dari, $sampai, max(1, (int) ($opsi['top'] ?? 20))),
            'laba' => $this->laba($dari, $sampai),
            'stok' => $this->stok((bool) ($opsi['hanya_menipis'] ?? false)),
            default => throw new InvalidArgumentException("Jenis laporan tidak dikenal: {$jenis}"),
        };

        return ['jenis' => $jenis, 'judul' => 'Laporan ' . self::JENIS[$jenis]] + $laporan + [
            'periode' => $jenis === 'stok'
                ? 'Posisi ' . Tanggal::pendek(now()) . ' ' . now()->format('H:i') . ' WIB'
                : Tanggal::pendek($dari) . ' - ' . Tanggal::pendek($sampai),
        ];
    }

    // ------------------------------------------------------------------ penjualan

    private function penjualan(Carbon $dari, Carbon $sampai, bool $bulanan): array
    {
        $grup = $bulanan ? "DATE_FORMAT(p.tgl_penjualan, '%Y-%m')" : 'DATE(p.tgl_penjualan)';
        $b = self::BARIS;

        $data = $this->dasar($dari, $sampai, true)
            ->selectRaw("{$grup} as grup, COUNT(DISTINCT p.id_penjualan) as transaksi, SUM(d.qty_detail_penjualan) as item,
                SUM(CASE WHEN b.metode_pembayaran = 'cash' THEN {$b} ELSE 0 END) as cash,
                SUM(CASE WHEN b.metode_pembayaran = 'qris' THEN {$b} ELSE 0 END) as qris,
                SUM({$b}) as total")
            ->groupBy('grup')
            ->get()
            ->keyBy('grup');

        $baris = [];
        $periode = $bulanan
            ? CarbonPeriod::create($dari->copy()->startOfMonth(), '1 month', $sampai)
            : CarbonPeriod::create($dari, $sampai);

        foreach ($periode as $t) {
            $r = $data->get($bulanan ? $t->format('Y-m') : $t->toDateString());
            $baris[] = [
                $bulanan ? Tanggal::bulan($t) : Tanggal::pendek($t),
                (int) ($r->transaksi ?? 0), (int) ($r->item ?? 0),
                (float) ($r->cash ?? 0), (float) ($r->qris ?? 0), (float) ($r->total ?? 0),
            ];
        }

        $trx = array_sum(array_column($baris, 1));
        $omzet = array_sum(array_column($baris, 5));

        return [
            'kolom' => [
                ['label' => $bulanan ? 'Bulan' : 'Tanggal', 'tipe' => 'teks'],
                ['label' => 'Transaksi', 'tipe' => 'angka'], ['label' => 'Item', 'tipe' => 'angka'],
                ['label' => 'Cash', 'tipe' => 'rupiah'], ['label' => 'QRIS', 'tipe' => 'rupiah'], ['label' => 'Total', 'tipe' => 'rupiah'],
            ],
            'baris' => $baris,
            'total' => ['Total', $trx, array_sum(array_column($baris, 2)), array_sum(array_column($baris, 3)), array_sum(array_column($baris, 4)), $omzet],
            'ringkasan' => [
                ['label' => 'Total Penjualan', 'nilai' => $omzet, 'tipe' => 'rupiah'],
                ['label' => 'Jumlah Transaksi', 'nilai' => $trx, 'tipe' => 'angka'],
                ['label' => 'Item Terjual', 'nilai' => array_sum(array_column($baris, 2)), 'tipe' => 'angka'],
                ['label' => 'Rata-rata per Transaksi', 'nilai' => $trx > 0 ? $omzet / $trx : 0, 'tipe' => 'rupiah'],
            ],
            'catatan' => ['Hanya transaksi berstatus selesai. Transaksi batal, menunggu bayar, dan diproses tidak dihitung.'],
        ];
    }

    // ------------------------------------------------------------------ per metode

    private function metode(Carbon $dari, Carbon $sampai): array
    {
        $data = $this->dasar($dari, $sampai, true)
            ->selectRaw("COALESCE(b.metode_pembayaran, 'lainnya') as metode, COUNT(DISTINCT p.id_penjualan) as transaksi,
                SUM(d.qty_detail_penjualan) as item, SUM(" . self::BARIS . ') as omzet')
            ->groupBy('metode')
            ->get()
            ->keyBy('metode');

        $total = (float) $data->sum('omzet');
        $baris = [];
        foreach (['cash' => 'Cash', 'qris' => 'QRIS', 'lainnya' => 'Lain-lain'] as $kunci => $label) {
            if ($kunci === 'lainnya' && ! $data->has($kunci)) {
                continue;
            }
            $r = $data->get($kunci);
            $omzet = (float) ($r->omzet ?? 0);
            $baris[] = [$label, (int) ($r->transaksi ?? 0), (int) ($r->item ?? 0), $omzet, $total > 0 ? $omzet / $total * 100 : 0.0];
        }

        $trx = array_sum(array_column($baris, 1));

        return [
            'kolom' => [
                ['label' => 'Metode', 'tipe' => 'teks'], ['label' => 'Transaksi', 'tipe' => 'angka'], ['label' => 'Item', 'tipe' => 'angka'],
                ['label' => 'Penjualan', 'tipe' => 'rupiah'], ['label' => 'Porsi', 'tipe' => 'persen'],
            ],
            'baris' => $baris,
            'total' => ['Total', $trx, array_sum(array_column($baris, 2)), $total, $total > 0 ? 100.0 : 0.0],
            'ringkasan' => [
                ['label' => 'Total Penjualan', 'nilai' => $total, 'tipe' => 'rupiah'],
                ['label' => 'Jumlah Transaksi', 'nilai' => $trx, 'tipe' => 'angka'],
            ],
            'catatan' => ['Metode diambil dari pembayaran yang berstatus berhasil.'],
        ];
    }

    // ------------------------------------------------------------------ barang terlaris

    private function terlaris(Carbon $dari, Carbon $sampai, int $top): array
    {
        $rows = $this->dasar($dari, $sampai, false)
            ->join('barang as br', 'br.id_barang', '=', 'd.id_barang')
            ->join('kategori as k', 'k.id_kategori', '=', 'br.id_kategori')
            ->selectRaw('br.kode_barang, br.nama_barang, k.nama_kategori, SUM(d.qty_detail_penjualan) as terjual, SUM(' . self::BARIS . ') as omzet')
            ->groupBy('d.id_barang', 'br.kode_barang', 'br.nama_barang', 'k.nama_kategori')
            ->orderByDesc('terjual')->orderByDesc('omzet')->orderBy('br.nama_barang')
            ->limit($top)
            ->get();

        $baris = [];
        foreach ($rows as $i => $r) {
            $baris[] = [$i + 1, $r->kode_barang, $r->nama_barang, $r->nama_kategori, (int) $r->terjual, (float) $r->omzet];
        }

        return [
            'kolom' => [
                ['label' => '#', 'tipe' => 'angka'], ['label' => 'Kode', 'tipe' => 'teks'], ['label' => 'Nama Barang', 'tipe' => 'teks'],
                ['label' => 'Kategori', 'tipe' => 'teks'], ['label' => 'Terjual', 'tipe' => 'angka'], ['label' => 'Penjualan', 'tipe' => 'rupiah'],
            ],
            'baris' => $baris,
            'total' => null,
            'ringkasan' => [
                ['label' => 'Barang Ditampilkan', 'nilai' => count($baris), 'tipe' => 'angka'],
                ['label' => 'Unit Terjual', 'nilai' => array_sum(array_column($baris, 4)), 'tipe' => 'angka'],
                ['label' => 'Penjualan', 'nilai' => array_sum(array_column($baris, 5)), 'tipe' => 'rupiah'],
            ],
            'catatan' => ["Urut menurut jumlah terjual, {$top} teratas."],
        ];
    }

    // ------------------------------------------------------------------ laba kotor

    private function laba(Carbon $dari, Carbon $sampai): array
    {
        $rows = $this->dasar($dari, $sampai, false)
            ->join('barang as br', 'br.id_barang', '=', 'd.id_barang')
            ->selectRaw('d.id_barang, br.kode_barang, br.nama_barang, SUM(d.qty_detail_penjualan) as terjual, SUM(' . self::BARIS . ') as omzet')
            ->groupBy('d.id_barang', 'br.kode_barang', 'br.nama_barang')
            ->orderByDesc('omzet')
            ->get();

        $hargaBeli = $this->hargaBeliTerakhir();
        $baris = [];
        $omzetAll = $omzetDikenal = $hppDikenal = 0.0;
        $tanpaBeli = 0;
        $terjualAll = 0;

        foreach ($rows as $r) {
            $omzet = (float) $r->omzet;
            $terjual = (int) $r->terjual;
            $hb = $hargaBeli[$r->id_barang] ?? null;
            $hpp = $hb !== null ? $hb * $terjual : null;
            $laba = $hpp !== null ? $omzet - $hpp : null;

            $omzetAll += $omzet;
            $terjualAll += $terjual;
            if ($hpp !== null) {
                $omzetDikenal += $omzet;
                $hppDikenal += $hpp;
            } else {
                $tanpaBeli++;
            }

            $baris[] = [$r->kode_barang, $r->nama_barang, $terjual, $omzet, $hb, $hpp, $laba, ($laba !== null && $omzet > 0) ? $laba / $omzet * 100 : null];
        }

        $labaTotal = $omzetDikenal - $hppDikenal;
        $margin = $omzetDikenal > 0 ? $labaTotal / $omzetDikenal * 100 : null;

        $catatan = ['HPP = harga beli terakhir (dari pembelian berstatus diterima) x jumlah terjual. Ini perkiraan, bukan HPP historis per transaksi.'];
        if ($tanpaBeli > 0) {
            $catatan[] = "{$tanpaBeli} barang belum punya data pembelian diterima sehingga tidak dihitung dalam HPP dan laba.";
        }

        return [
            'kolom' => [
                ['label' => 'Kode', 'tipe' => 'teks'], ['label' => 'Nama Barang', 'tipe' => 'teks'], ['label' => 'Terjual', 'tipe' => 'angka'],
                ['label' => 'Penjualan', 'tipe' => 'rupiah'], ['label' => 'Harga Beli Terakhir', 'tipe' => 'rupiah'], ['label' => 'HPP', 'tipe' => 'rupiah'],
                ['label' => 'Laba Kotor', 'tipe' => 'rupiah'], ['label' => 'Margin', 'tipe' => 'persen'],
            ],
            'baris' => $baris,
            'total' => ['Total', null, $terjualAll, $omzetAll, null, $hppDikenal, $labaTotal, $margin],
            'ringkasan' => [
                ['label' => 'Penjualan', 'nilai' => $omzetAll, 'tipe' => 'rupiah'],
                ['label' => 'HPP', 'nilai' => $hppDikenal, 'tipe' => 'rupiah'],
                ['label' => 'Laba Kotor', 'nilai' => $labaTotal, 'tipe' => 'rupiah'],
                ['label' => 'Margin', 'nilai' => $margin, 'tipe' => 'persen'],
            ],
            'catatan' => $catatan,
        ];
    }

    /** @return array<int,float> id_barang => harga beli pada pembelian 'diterima' terbaru */
    private function hargaBeliTerakhir(): array
    {
        $peta = [];
        DB::table('detail_pembelian as dp')
            ->join('pembelian as pb', 'pb.id_pembelian', '=', 'dp.id_pembelian')
            ->where('pb.status_pembelian', 'diterima')
            ->orderBy('pb.tgl_pembelian')->orderBy('pb.id_pembelian')
            ->get(['dp.id_barang', 'dp.harga_beli_detail_pembelian'])
            ->each(function ($r) use (&$peta) {
                $peta[$r->id_barang] = (float) $r->harga_beli_detail_pembelian;   // yang terbaru menimpa
            });

        return $peta;
    }

    // ------------------------------------------------------------------ stok

    private function stok(bool $hanyaMenipis): array
    {
        $barang = Barang::aktif()->with('kategori')
            ->when($hanyaMenipis, fn ($q) => $q->stokMenipis())
            ->orderBy('nama_barang')->get();

        $baris = [];
        $menipis = $habis = 0;
        foreach ($barang as $b) {
            $status = $b->stok_barang <= 0 ? 'Habis' : ($b->stok_barang <= $b->stok_min_barang ? 'Menipis' : 'Aman');
            $habis += $status === 'Habis' ? 1 : 0;
            $menipis += $status !== 'Aman' ? 1 : 0;
            $baris[] = [
                $b->kode_barang, $b->nama_barang, $b->kategori?->nama_kategori ?? '-', $b->stok_barang, $b->stok_min_barang,
                $status, (float) $b->harga_jual_barang, $b->stok_barang * (float) $b->harga_jual_barang,
            ];
        }

        $unit = array_sum(array_column($baris, 3));
        $nilai = array_sum(array_column($baris, 7));

        return [
            'kolom' => [
                ['label' => 'Kode', 'tipe' => 'teks'], ['label' => 'Nama Barang', 'tipe' => 'teks'], ['label' => 'Kategori', 'tipe' => 'teks'],
                ['label' => 'Stok', 'tipe' => 'angka'], ['label' => 'Minimum', 'tipe' => 'angka'], ['label' => 'Status', 'tipe' => 'teks'],
                ['label' => 'Harga Jual', 'tipe' => 'rupiah'], ['label' => 'Nilai Persediaan', 'tipe' => 'rupiah'],
            ],
            'baris' => $baris,
            'total' => ['Total', null, null, $unit, null, null, null, $nilai],
            'ringkasan' => [
                ['label' => 'Jumlah Produk', 'nilai' => count($baris), 'tipe' => 'angka'],
                ['label' => 'Total Unit Stok', 'nilai' => $unit, 'tipe' => 'angka'],
                ['label' => 'Nilai Persediaan', 'nilai' => $nilai, 'tipe' => 'rupiah'],
                ['label' => 'Stok Menipis / Habis', 'nilai' => $menipis . ' / ' . $habis, 'tipe' => 'teks'],
            ],
            'catatan' => ['Hanya barang aktif. Menipis = stok <= stok minimum. Nilai persediaan dihitung dengan harga jual saat ini.'],
        ];
    }

    // ------------------------------------------------------------------ dasar query

    private function dasar(Carbon $dari, Carbon $sampai, bool $denganPembayaran): Builder
    {
        $q = DB::table('penjualan as p')
            ->join('detail_penjualan as d', 'd.id_penjualan', '=', 'p.id_penjualan')
            ->where('p.status_penjualan', 'selesai')
            ->whereBetween('p.tgl_penjualan', [$dari, $sampai]);

        if ($denganPembayaran) {
            $q->leftJoin('pembayaran as b', fn ($j) => $j->on('b.id_penjualan', '=', 'p.id_penjualan')->where('b.status_pembayaran', '=', 'berhasil'));
        }

        return $q;
    }
}
