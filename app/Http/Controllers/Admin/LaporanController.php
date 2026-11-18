<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanRequest;
use App\Models\Pengaturan;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $layanan)
    {
    }

    public function index(LaporanRequest $request)
    {
        return view('admin.laporan.index', [
            'laporan' => $this->buat($request),
            'daftarJenis' => LaporanService::JENIS,
            'dari' => $request->dari(),
            'sampai' => $request->sampai(),
            'opsi' => $request->opsi(),
        ]);
    }

    public function excel(LaporanRequest $request)
    {
        return Excel::download(new LaporanExport($this->buat($request)), $this->namaFile($request, 'xlsx'));
    }

    public function pdf(LaporanRequest $request)
    {
        return Pdf::loadView('admin.laporan.pdf', [
            'laporan' => $this->buat($request),
            'koperasi' => Pengaturan::ambil('nama_koperasi', config('koperasi.nama')),
            'pencetak' => $request->user()->name,
        ])
            ->setPaper('a4', in_array($request->jenis(), ['laba', 'stok', 'terlaris'], true) ? 'landscape' : 'portrait')
            ->stream($this->namaFile($request, 'pdf'));
    }

    private function buat(LaporanRequest $request): array
    {
        return $this->layanan->buat($request->jenis(), $request->dari(), $request->sampai(), $request->opsi());
    }

    private function namaFile(LaporanRequest $request, string $ekstensi): string
    {
        $rentang = $request->jenis() === 'stok'
            ? now()->format('Ymd')
            : $request->dari()->format('Ymd') . '-' . $request->sampai()->format('Ymd');

        return "laporan-{$request->jenis()}-{$rentang}.{$ekstensi}";
    }
}
