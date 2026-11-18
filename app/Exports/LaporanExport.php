<?php

namespace App\Exports;

use App\Services\LaporanService;
use App\Support\FormatLaporan;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Ekspor Excel generik dari struktur keluaran LaporanService. Angka ditulis sebagai angka (bukan teks). */
class LaporanExport implements FromArray, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private const BARIS_HEADER = 5;

    public function __construct(private array $laporan)
    {
    }

    public function array(): array
    {
        $l = $this->laporan;
        $rows = [
            [$l['judul']],
            [$l['periode']],
            ['Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB'],
            [],
            array_column($l['kolom'], 'label'),
        ];

        foreach ($l['baris'] as $baris) {
            $rows[] = $this->nilai($baris);
        }
        if ($l['total']) {
            $rows[] = $this->nilai($l['total']);
        }

        $rows[] = [];
        $rows[] = ['Ringkasan'];
        foreach ($l['ringkasan'] as $r) {
            $rows[] = [$r['label'], FormatLaporan::sel($r['nilai'], $r['tipe'])];
        }
        foreach ($l['catatan'] as $c) {
            $rows[] = [];
            $rows[] = ['Catatan: ' . $c];
        }

        return $rows;
    }

    public function title(): string
    {
        return Str::limit(LaporanService::JENIS[$this->laporan['jenis']], 31, '');
    }

    public function columnFormats(): array
    {
        $format = ['rupiah' => '"Rp" #,##0', 'angka' => '#,##0', 'persen' => '0.0"%"'];
        $hasil = [];
        foreach ($this->laporan['kolom'] as $i => $k) {
            if (isset($format[$k['tipe']])) {
                $hasil[Coordinate::stringFromColumnIndex($i + 1)] = $format[$k['tipe']];
            }
        }

        return $hasil;
    }

    public function styles(Worksheet $sheet): array
    {
        $gaya = [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            self::BARIS_HEADER => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0B1F6B']],
            ],
        ];
        if ($this->laporan['total']) {
            $gaya[self::BARIS_HEADER + count($this->laporan['baris']) + 1] = ['font' => ['bold' => true]];
        }

        return $gaya;
    }

    /** Teks tetap teks; angka/rupiah/persen tetap numerik (null -> sel kosong). */
    private function nilai(array $baris): array
    {
        $hasil = [];
        foreach ($baris as $i => $v) {
            $tipe = $this->laporan['kolom'][$i]['tipe'] ?? 'teks';
            $hasil[] = $v === null ? null : ($tipe === 'teks' ? (string) $v : (float) $v);
        }

        return $hasil;
    }
}
