<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Barang;
use App\Models\Penjualan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /** Omzet = total penjualan berstatus 'selesai'. */
    public function omzet(Carbon $dari, Carbon $sampai): float
    {
        return (float) DB::table('penjualan as p')
            ->join('detail_penjualan as d', 'd.id_penjualan', '=', 'p.id_penjualan')
            ->where('p.status_penjualan', 'selesai')
            ->whereBetween('p.tgl_penjualan', [$dari, $sampai])
            ->sum(DB::raw('d.qty_detail_penjualan * d.harga_satuan_detail_penjualan'));
    }

    /** Jumlah pesanan = semua penjualan yang tidak batal. */
    public function jumlahPesanan(Carbon $hari): int
    {
        return Penjualan::whereBetween('tgl_penjualan', [$hari->copy()->startOfDay(), $hari->copy()->endOfDay()])
            ->where('status_penjualan', '!=', 'batal')
            ->count();
    }

    public function ringkasan(): array
    {
        $hariIni = now()->startOfDay();
        $kemarin = $hariIni->copy()->subDay();

        $jualIni = $this->omzet($hariIni, $hariIni->copy()->endOfDay());
        $jualLalu = $this->omzet($kemarin, $kemarin->copy()->endOfDay());
        $pesanIni = $this->jumlahPesanan($hariIni);
        $pesanLalu = $this->jumlahPesanan($kemarin);

        $akhirBulanLalu = now()->startOfMonth()->subDay()->toDateString();
        $anggotaIni = Anggota::aktif()->count();
        $anggotaBulanLalu = Anggota::aktif()->whereDate('tgl_daftar_anggota', '<=', $akhirBulanLalu)->count();

        return [
            'penjualan' => ['hari_ini' => $jualIni, 'persen' => $this->persen($jualIni, $jualLalu)],
            'pesanan' => ['hari_ini' => $pesanIni, 'persen' => $this->persen($pesanIni, $pesanLalu)],
            'produk' => [
                'total' => Barang::count(),
                'baru' => Barang::where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'anggota' => ['total' => $anggotaIni, 'persen' => $this->persen($anggotaIni, $anggotaBulanLalu)],
        ];
    }

    /** @param '7'|'30'|'bulan' $rentang */
    public function grafik(string $rentang = '7'): array
    {
        $akhir = now()->startOfDay();
        [$mulai, $judul] = match ($rentang) {
            '30' => [$akhir->copy()->subDays(29), '30 Hari Terakhir'],
            'bulan' => [now()->startOfMonth(), 'Bulan Ini'],
            default => [$akhir->copy()->subDays(6), '7 Hari Terakhir'],
        };

        $baris = DB::table('penjualan as p')
            ->join('detail_penjualan as d', 'd.id_penjualan', '=', 'p.id_penjualan')
            ->where('p.status_penjualan', 'selesai')
            ->whereBetween('p.tgl_penjualan', [$mulai, $akhir->copy()->endOfDay()])
            ->selectRaw('DATE(p.tgl_penjualan) as tgl, SUM(d.qty_detail_penjualan * d.harga_satuan_detail_penjualan) as total')
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $labels = [];
        $data = [];
        foreach (CarbonPeriod::create($mulai, $akhir) as $tgl) {
            $labels[] = $tgl->format('j') . ' ' . self::BULAN[$tgl->month - 1];
            $data[] = (float) ($baris[$tgl->toDateString()] ?? 0);
        }

        return ['rentang' => $rentang, 'judul' => $judul, 'labels' => $labels, 'data' => $data];
    }

    public function transaksiTerbaru(int $jumlah = 8): Collection
    {
        return Penjualan::with(['anggota', 'details.barang', 'pembayaranTerakhir'])
            ->orderByDesc('tgl_penjualan')
            ->orderByDesc('id_penjualan')
            ->limit($jumlah)
            ->get();
    }

    /** Barang aktif dengan stok_barang <= stok_min_barang. */
    public function stokMenipis(int $jumlah = 5): array
    {
        $query = Barang::aktif()->stokMenipis();

        return [
            'daftar' => (clone $query)->orderBy('stok_barang')->orderBy('nama_barang')->limit($jumlah)->get(),
            'total' => $query->count(),
        ];
    }

    private function persen(float|int $sekarang, float|int $sebelumnya): ?int
    {
        return $sebelumnya > 0 ? (int) round(($sekarang - $sebelumnya) / $sebelumnya * 100) : null;
    }
}
