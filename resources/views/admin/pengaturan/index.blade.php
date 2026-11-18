@extends('layouts.admin')
@section('judul', 'Pengaturan')

@section('isi')
<div class="mx-auto max-w-3xl">
    <h1 class="text-2xl font-bold text-navy">Pengaturan</h1>
    <p class="mt-2 text-sm text-slate-600">Atur identitas koperasi dan QRIS. Format gambar JPG, JPEG, PNG, atau WEBP, maksimal 2 MB.</p>

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label for="logo_koperasi" class="mb-1 block text-sm font-semibold text-slate-700">Logo koperasi</label>
            <div class="flex items-center gap-4">
                @if ($logoKoperasi && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoKoperasi))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi saat ini" class="h-16 w-16 rounded-full border border-slate-200 bg-white object-contain p-1">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-kuning text-xs font-bold text-navy">UM</div>
                @endif
                <div class="flex-1">
                    <input id="logo_koperasi" name="logo_koperasi" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-slate-300 bg-white p-3 text-sm">
                    @error('logo_koperasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-slate-500">Tampil di header toko, halaman masuk, dan dashboard admin. Disarankan rasio persegi (contoh 256×256 px).</p>
                </div>
            </div>
        </div>

        <div>
            <label for="no_admin" class="mb-1 block text-sm font-semibold text-slate-700">Nomor WhatsApp Admin (Floating WA & Kontak Toko)</label>
            <input id="no_admin" name="no_admin" type="text" value="{{ old('no_admin', $noAdmin ?? \App\Models\Pengaturan::noAdmin()) }}" placeholder="Contoh: 081234567890" maxlength="25" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-navy focus:outline-none focus:ring-2 focus:ring-navy/20">
            @error('no_admin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500">Nomor ini terhubung ke tombol WhatsApp melayang di toko publik dan informasi kontak koperasi.</p>
        </div>

        <div>
            <label for="nama_merchant_qris" class="mb-1 block text-sm font-semibold text-slate-700">Nama merchant</label>
            <input id="nama_merchant_qris" name="nama_merchant_qris" value="{{ old('nama_merchant_qris', $namaMerchant) }}" required maxlength="100" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            @error('nama_merchant_qris')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="qris_statis" class="mb-1 block text-sm font-semibold text-slate-700">String QRIS statis</label>
            <textarea id="qris_statis" name="qris_statis" rows="4" required maxlength="512" class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-xs">{{ old('qris_statis', $qrisStatis) }}</textarea>
            @error('qris_statis')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500">Tempel payload teks QRIS statis dari penyedia. Format dan checksum akan diperiksa saat disimpan.</p>
        </div>

        <div>
            <label for="gambar_qris" class="mb-1 block text-sm font-semibold text-slate-700">Gambar QRIS</label>
            <input id="gambar_qris" name="gambar_qris" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-slate-300 bg-white p-3 text-sm">
            @error('gambar_qris')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500">Pilih gambar QRIS dari perangkatmu. Format JPG, JPEG, PNG, atau WEBP, maksimal 2 MB.</p>
        </div>

        @if (session('sukses'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm font-medium text-green-800">{{ session('sukses') }}</div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">Periksa kembali isian di atas. Ada data yang belum valid.</div>
        @endif

        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:12px; border-bottom:1px solid #e2e8f0; padding-bottom:20px; margin-top:20px;">
            <button type="submit" style="display:inline-block !important; visibility:visible !important; opacity:1 !important; cursor:pointer; border:0; border-radius:8px; background:#15803d; color:#ffffff !important; padding:12px 24px; font-size:16px; line-height:1.5; font-weight:700; box-shadow:0 2px 5px rgba(0,0,0,.15);">
                Simpan Pengaturan
            </button>
            <span style="font-size:14px; color:#64748b;">Klik setelah memilih gambar untuk menyimpan perubahan.</span>
        </div>

        @if ($gambarQris && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarQris))
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="mb-3 text-sm font-semibold text-slate-700">QRIS yang sedang digunakan</p>
                <img src="{{ \Illuminate\Support\Facades\Storage::url($gambarQris) }}" alt="QRIS koperasi saat ini" class="mx-auto max-h-80 rounded-lg object-contain">
            </div>
        @else
            <div class="rounded-lg bg-amber-50 p-4 text-sm text-amber-800">Belum ada gambar QRIS yang diunggah.</div>
        @endif

    </form>
</div>
@endsection
