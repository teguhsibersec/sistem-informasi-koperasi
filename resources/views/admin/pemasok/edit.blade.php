@extends('layouts.admin')
@section('judul', 'Edit Pemasok')

@section('isi')
<a href="{{ route('admin.pembelian.pemasok.index') }}" class="text-xs text-navy hover:underline">← Semua pemasok</a>
<h1 class="mt-1 text-2xl font-bold text-navy">Edit Pemasok</h1>

<form method="POST" action="{{ route('admin.pembelian.pemasok.update', $pemasok) }}" class="mt-4 max-w-md space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf @method('PUT')
    <div><label class="label" for="nama_pemasok">Nama *</label><input id="nama_pemasok" name="nama_pemasok" value="{{ old('nama_pemasok', $pemasok->nama_pemasok) }}" maxlength="100" required class="input"></div>
    <div><label class="label" for="telepon_pemasok">Telepon</label><input id="telepon_pemasok" name="telepon_pemasok" value="{{ old('telepon_pemasok', $pemasok->telepon_pemasok) }}" maxlength="15" class="input"></div>
    <div><label class="label" for="alamat_pemasok">Alamat</label><input id="alamat_pemasok" name="alamat_pemasok" value="{{ old('alamat_pemasok', $pemasok->alamat_pemasok) }}" maxlength="200" class="input"></div>
    <button class="btn-navy">Simpan Perubahan</button>
</form>
@endsection
