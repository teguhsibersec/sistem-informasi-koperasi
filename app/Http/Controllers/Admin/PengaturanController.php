<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\Qris\QrisDinamis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan.index', [
            'logoKoperasi' => Pengaturan::ambil('logo_koperasi'),
            'gambarQris' => Pengaturan::ambil('qris_gambar'),
            'qrisStatis' => Pengaturan::ambil('qris_statis', ''),
            'namaMerchant' => Pengaturan::ambil('nama_merchant_qris', config('koperasi.nama')),
            'noAdmin' => Pengaturan::noAdmin(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'no_admin' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\-\s]+$/'],
            'nama_merchant_qris' => ['required', 'string', 'max:100'],
            'qris_statis' => ['required', 'string', 'max:512', function ($attribute, $value, $fail) {
                try {
                    QrisDinamis::urai(trim($value));
                } catch (InvalidArgumentException) {
                    $fail('String QRIS tidak valid. Pastikan format dan checksum QRIS benar.');
                }
            }],
            'gambar_qris' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_koperasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'no_admin.regex' => 'Format nomor WhatsApp hanya boleh angka, spasi, tanda plus, atau tanda minus.',
            'nama_merchant_qris.required' => 'Nama merchant wajib diisi.',
            'qris_statis.required' => 'String QRIS statis wajib diisi.',
            'qris_statis.max' => 'String QRIS maksimal 512 karakter.',
            'gambar_qris.image' => 'File QRIS harus berupa gambar.',
            'gambar_qris.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'gambar_qris.max' => 'Ukuran gambar maksimal 2 MB.',
            'logo_koperasi.image' => 'Logo harus berupa gambar.',
            'logo_koperasi.mimes' => 'Format logo harus JPG, JPEG, PNG, atau WEBP.',
            'logo_koperasi.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        if (array_key_exists('no_admin', $data)) {
            Pengaturan::simpan('no_admin', trim((string) $data['no_admin']));
        }
        Pengaturan::simpan('nama_merchant_qris', $data['nama_merchant_qris']);
        Pengaturan::simpan('qris_statis', trim($data['qris_statis']));

        if ($request->hasFile('gambar_qris')) {
            $lama = Pengaturan::ambil('qris_gambar');
            $path = $request->file('gambar_qris')->store('qris', 'public');
            Pengaturan::simpan('qris_gambar', $path);
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
        }

        if ($request->hasFile('logo_koperasi')) {
            $lama = Pengaturan::ambil('logo_koperasi');
            $path = $request->file('logo_koperasi')->store('logo', 'public');
            Pengaturan::simpan('logo_koperasi', $path);
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
        }

        return redirect()->route('admin.pengaturan.index')->with('sukses', 'Pengaturan berhasil disimpan.');
    }
}
