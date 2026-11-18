<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard)
    {
    }

    public function index()
    {
        $menipis = $this->dashboard->stokMenipis();

        return view('admin.dashboard', [
            'ringkasan' => $this->dashboard->ringkasan(),
            'grafik' => $this->dashboard->grafik('7'),
            'transaksi' => $this->dashboard->transaksiTerbaru(),
            'stokMenipis' => $menipis['daftar'],
            'totalMenipis' => $menipis['total'],
            'noAdmin' => Pengaturan::noAdmin(),
        ]);
    }

    public function grafik(Request $request): JsonResponse
    {
        $request->validate(['rentang' => ['nullable', 'in:7,30,bulan']]);

        return response()->json($this->dashboard->grafik($request->input('rentang', '7')));
    }

    public function updateNoAdmin(Request $request)
    {
        $data = $request->validate([
            'no_admin' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'no_admin.required' => 'Nomor WhatsApp admin wajib diisi.',
            'no_admin.max' => 'Nomor WhatsApp maksimal 25 karakter.',
            'no_admin.regex' => 'Format nomor WhatsApp hanya boleh angka, spasi, tanda plus, atau tanda minus.',
        ]);

        Pengaturan::simpan('no_admin', trim($data['no_admin']));

        return back()->with('sukses', 'Nomor WhatsApp admin berhasil diperbarui menjadi ' . $data['no_admin'] . '.');
    }
}
