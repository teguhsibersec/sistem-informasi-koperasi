<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemasok;
use App\Support\Aktivitas;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $e = addcslashes(trim((string) $request->query('q', '')), '%_\\');

        return view('admin.pemasok.index', [
            'pemasok' => Pemasok::withCount('pembelian')
                ->when($e !== '', fn ($x) => $x->where('nama_pemasok', 'like', "%{$e}%"))
                ->orderBy('nama_pemasok')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $pemasok = Pemasok::create($request->validate($this->aturan()));
        Aktivitas::catat('tambah', 'pemasok', $pemasok->id_pemasok, ['nama' => $pemasok->nama_pemasok]);

        return redirect()->route('admin.pembelian.pemasok.index')->with('sukses', 'Pemasok ditambahkan.');
    }

    public function edit(Pemasok $pemasok)
    {
        return view('admin.pemasok.edit', ['pemasok' => $pemasok]);
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $pemasok->update($request->validate($this->aturan()));
        Aktivitas::catat('ubah', 'pemasok', $pemasok->id_pemasok, $pemasok->getChanges());

        return redirect()->route('admin.pembelian.pemasok.index')->with('sukses', 'Pemasok diperbarui.');
    }

    public function destroy(Pemasok $pemasok)
    {
        $n = $pemasok->pembelian()->count();
        if ($n > 0) {
            return back()->with('galat', "Pemasok memiliki {$n} pembelian dan tidak dapat dihapus.");
        }

        $pemasok->delete();
        Aktivitas::catat('hapus', 'pemasok', $pemasok->id_pemasok, ['nama' => $pemasok->nama_pemasok]);

        return redirect()->route('admin.pembelian.pemasok.index')->with('sukses', 'Pemasok dihapus.');
    }

    private function aturan(): array
    {
        return [
            'nama_pemasok' => ['required', 'string', 'max:100'],
            'telepon_pemasok' => ['nullable', 'string', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
            'alamat_pemasok' => ['nullable', 'string', 'max:200'],
        ];
    }
}
