<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kode_barang')) {
            $this->merge(['kode_barang' => strtoupper(trim((string) $this->input('kode_barang')))]);
        }
    }

    public function rules(): array
    {
        $barang = $this->route('barang');

        return [
            // UNIQUE di database juga berlaku untuk produk yang sudah dihapus (soft delete), jadi cek tanpa filter.
            'kode_barang' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9\-_.]+$/', Rule::unique('barang', 'kode_barang')->ignore($barang?->id_barang, 'id_barang')],
            'nama_barang' => ['required', 'string', 'max:100'],
            'satuan_barang' => ['required', 'string', 'max:25'],
            'id_kategori' => ['required', 'integer', Rule::exists('kategori', 'id_kategori')->whereNull('deleted_at')],
            'harga_jual_barang' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stok_min_barang' => ['required', 'integer', 'min:0', 'max:100000'],
            'deskripsi_barang' => ['nullable', 'string', 'max:2000'],
            'status_barang' => ['required', 'in:aktif,nonaktif'],
            'gambar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar' => ['nullable', 'boolean'],
            // Stok hanya diisi saat membuat produk; selanjutnya lewat Stok Barang (penyesuaian) atau Pembelian.
            'stok_awal' => $this->isMethod('POST') ? ['nullable', 'integer', 'min:0', 'max:1000000'] : ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.regex' => 'Kode hanya boleh huruf, angka, tanda minus, titik, atau garis bawah (tanpa spasi).',
            'kode_barang.unique' => 'Kode barang sudah dipakai (termasuk produk yang sudah dihapus).',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan_barang.required' => 'Satuan wajib diisi (mis. pcs, botol, rim).',
            'id_kategori.required' => 'Pilih kategori.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'harga_jual_barang.required' => 'Harga jual wajib diisi.',
            'harga_jual_barang.min' => 'Harga jual tidak boleh negatif.',
            'stok_min_barang.required' => 'Stok minimum wajib diisi.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB.',
            'stok_awal.prohibited' => 'Stok tidak diubah dari sini. Gunakan menu Stok Barang atau Pembelian.',
        ];
    }
}
