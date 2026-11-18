<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutKasirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
            'metode' => ['required', 'in:cash,qris'],
            'id_anggota' => ['nullable', 'integer', 'exists:anggota,id_anggota'],
            'uang_diterima' => ['required_if:metode,cash', 'nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'metode.in' => 'Pilih metode pembayaran Cash atau QRIS.',
            'uang_diterima.required_if' => 'Masukkan uang yang diterima.',
        ];
    }
}
