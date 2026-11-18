<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengurus;
use App\Support\Aktivitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Form Data Pengurus (skripsi Gambar 4.6): Simpan, Edit, Hapus, Cari, Laporan. */
class PengurusController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pengurus.index', [
            'pengurus' => $this->cari($request)->paginate(10)->withQueryString(),
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['kode_pengurus' => strtoupper(trim((string) $request->input('kode_pengurus')))]);
        $pengurus = Pengurus::create($request->validate($this->aturan()));
        Aktivitas::catat('tambah', 'pengurus', $pengurus->id_pengurus, ['nama' => $pengurus->nama_pengurus]);

        return redirect()->route('admin.pengurus.index')->with('sukses', 'Pengurus disimpan.');
    }

    public function edit(Pengurus $pengurus)
    {
        return view('admin.pengurus.edit', ['pengurus' => $pengurus]);
    }

    public function update(Request $request, Pengurus $pengurus)
    {
        $request->merge(['kode_pengurus' => strtoupper(trim((string) $request->input('kode_pengurus')))]);
        $pengurus->update($request->validate($this->aturan($pengurus)));
        Aktivitas::catat('ubah', 'pengurus', $pengurus->id_pengurus, $pengurus->getChanges());

        return redirect()->route('admin.pengurus.index')->with('sukses', 'Data pengurus diperbarui.');
    }

    public function destroy(Pengurus $pengurus)
    {
        if ($pengurus->users()->exists() || $pengurus->penjualan()->exists()) {
            return back()->with('galat', 'Pengurus ini memiliki akun login atau riwayat transaksi dan tidak dapat dihapus.');
        }

        $pengurus->delete();
        Aktivitas::catat('hapus', 'pengurus', $pengurus->id_pengurus, ['nama' => $pengurus->nama_pengurus]);

        return redirect()->route('admin.pengurus.index')->with('sukses', 'Pengurus dihapus.');
    }

    public function laporan(Request $request)
    {
        return Pdf::loadView('admin.pengurus.pdf', [
            'koperasi' => config('koperasi.nama'),
            'pengurus' => $this->cari($request)->get(),
            'dicetak' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape')->stream('laporan-data-pengurus.pdf');
    }

    private function cari(Request $request)
    {
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        return Pengurus::query()
            ->when($e !== '', fn ($x) => $x->where(fn ($w) => $w->where('kode_pengurus', 'like', "%{$e}%")
                ->orWhere('nama_pengurus', 'like', "%{$e}%")->orWhere('jabatan_pengurus', 'like', "%{$e}%")))
            ->orderBy('kode_pengurus');
    }

    private function aturan(?Pengurus $pengurus = null): array
    {
        return [
            'kode_pengurus' => ['required', 'string', 'size:3', 'alpha_num', Rule::unique('pengurus', 'kode_pengurus')->ignore($pengurus?->id_pengurus, 'id_pengurus')],
            'nama_pengurus' => ['required', 'string', 'max:100'],
            'jabatan_pengurus' => ['required', Rule::in(array_keys(Pengurus::JABATAN))],
            'alamat_pengurus' => ['nullable', 'string', 'max:100'],
            'kota_pengurus' => ['nullable', 'string', 'max:40'],
            'telepon_pengurus' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
        ];
    }
}
