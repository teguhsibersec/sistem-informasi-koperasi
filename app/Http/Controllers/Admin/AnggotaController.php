<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Support\Aktivitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Form Data Anggota (skripsi Gambar 4.7): Simpan, Edit, Hapus, Cari, Laporan. */
class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.anggota.index', [
            'anggota' => $this->cari($request)->paginate(10)->withQueryString(),
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());

        $anggota = DB::transaction(function () use ($data) {
            $terakhir = DB::table('anggota')->lockForUpdate()->orderByDesc('no_anggota')->value('no_anggota');
            $nomor = sprintf('A%05d', $terakhir ? ((int) substr($terakhir, 1)) + 1 : 1);

            return Anggota::create($data + ['no_anggota' => $nomor, 'tgl_daftar_anggota' => now()->toDateString()]);
        });
        Aktivitas::catat('tambah', 'anggota', $anggota->id_anggota, ['nama' => $anggota->nama_anggota]);

        return redirect()->route('admin.anggota.index')->with('sukses', 'Anggota disimpan dengan kode ' . $anggota->no_anggota . '.');
    }

    public function edit(Anggota $anggota)
    {
        return view('admin.anggota.edit', ['anggota' => $anggota]);
    }

    public function update(Request $request, Anggota $anggota)
    {
        $anggota->update($request->validate($this->aturan($anggota)));
        Aktivitas::catat('ubah', 'anggota', $anggota->id_anggota, $anggota->getChanges());

        return redirect()->route('admin.anggota.index')->with('sukses', 'Data anggota diperbarui.');
    }

    /** Anggota yang punya akun atau riwayat transaksi tidak dihapus; cukup dinonaktifkan. */
    public function destroy(Anggota $anggota)
    {
        $punyaAkun = DB::table('users')->where('id_anggota', $anggota->id_anggota)->exists();
        $punyaTransaksi = $anggota->penjualan()->exists();
        if ($punyaAkun || $punyaTransaksi) {
            return back()->with('galat', 'Anggota ini memiliki akun atau riwayat transaksi dan tidak dapat dihapus. Ubah statusnya menjadi nonaktif.');
        }

        $anggota->delete();
        Aktivitas::catat('hapus', 'anggota', $anggota->id_anggota, ['nama' => $anggota->nama_anggota]);

        return redirect()->route('admin.anggota.index')->with('sukses', 'Anggota dihapus.');
    }

    public function laporan(Request $request)
    {
        return Pdf::loadView('admin.anggota.pdf', [
            'koperasi' => config('koperasi.nama'),
            'anggota' => $this->cari($request)->get(),
            'dicetak' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape')->stream('laporan-data-anggota.pdf');
    }

    private function cari(Request $request)
    {
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        return Anggota::query()
            ->when($e !== '', fn ($x) => $x->where(fn ($w) => $w->where('no_anggota', 'like', "%{$e}%")
                ->orWhere('nis_anggota', 'like', "%{$e}%")->orWhere('nama_anggota', 'like', "%{$e}%")->orWhere('kelas_anggota', 'like', "%{$e}%")))
            ->orderBy('no_anggota');
    }

    private function aturan(?Anggota $anggota = null): array
    {
        return [
            'nis_anggota' => ['required', 'digits_between:4,20', Rule::unique('anggota', 'nis_anggota')->ignore($anggota?->id_anggota, 'id_anggota')],
            'nama_anggota' => ['required', 'string', 'max:100'],
            'kelas_anggota' => ['required', 'string', 'max:15'],
            'alamat_anggota' => ['nullable', 'string', 'max:100'],
            'kota_anggota' => ['nullable', 'string', 'max:35'],
            'no_hp_anggota' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
            'status_anggota' => $anggota ? ['required', 'in:aktif,nonaktif'] : ['prohibited'],
        ];
    }
}
