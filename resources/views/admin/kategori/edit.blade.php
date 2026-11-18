@extends('layouts.admin')
@section('judul', 'Edit Kategori')

@section('isi')
<a href="{{ route('admin.kategori.index') }}" class="text-xs text-navy hover:underline">← Semua kategori</a>
<h1 class="mt-1 text-2xl font-bold text-navy">Edit Kategori</h1>

<form method="POST" action="{{ route('admin.kategori.update', $kategori) }}" class="mt-4 max-w-md space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf @method('PUT')
    <div><label class="label" for="nama_kategori">Nama *</label><input id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" maxlength="50" required class="input"></div>
    <div>
        <label class="label" for="ikon">Ikon *</label>
        <select id="ikon" name="ikon" class="input">@foreach ($daftarIkon as $v => $l) <option value="{{ $v }}" @selected(old('ikon', $kategori->ikon) === $v)>{{ $l }}</option> @endforeach</select>
    </div>
    <p class="text-xs text-slate-400">Slug <span class="font-mono">{{ $kategori->slug_kategori }}</span> tidak berubah agar tautan toko tetap valid.</p>
    <button class="btn-navy">Simpan Perubahan</button>
</form>
@endsection
