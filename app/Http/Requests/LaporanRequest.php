<?php

namespace App\Http\Requests;

use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis' => ['nullable', Rule::in(array_keys(LaporanService::JENIS))],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'top' => ['nullable', 'integer', 'min:1', 'max:100'],
            'hanya_menipis' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.in' => 'Jenis laporan tidak dikenal.',
            'dari.date' => 'Tanggal awal tidak valid.',
            'sampai.date' => 'Tanggal akhir tidak valid.',
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! $v->errors()->any() && $this->jenis() === 'penjualan-harian' && $this->dari()->diffInDays($this->sampai()) > 366) {
                $v->errors()->add('sampai', 'Rentang laporan harian maksimal 366 hari. Gunakan laporan bulanan untuk rentang lebih panjang.');
            }
        });
    }

    public function jenis(): string
    {
        return $this->input('jenis') ?: 'penjualan-harian';
    }

    public function dari(): Carbon
    {
        return $this->filled('dari') ? Carbon::parse($this->input('dari'))->startOfDay() : now()->startOfMonth();
    }

    public function sampai(): Carbon
    {
        return $this->filled('sampai') ? Carbon::parse($this->input('sampai'))->endOfDay() : now()->endOfDay();
    }

    public function opsi(): array
    {
        return ['top' => (int) ($this->input('top') ?: 20), 'hanya_menipis' => $this->boolean('hanya_menipis')];
    }
}
