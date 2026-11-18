@extends('layouts.admin')
@section('judul', 'Edit Pengurus')

@section('isi')
<a href="{{ route('admin.pengurus.index') }}" class="text-xs text-navy hover:underline">← Data Pengurus</a>
<h1 class="mt-1 text-2xl font-bold text-navy">Edit Pengurus</h1>

<form method="POST" action="{{ route('admin.pengurus.update', $pengurus) }}" class="mt-4 space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf @method('PUT')
    @include('admin.pengurus._form', ['p' => $pengurus])
    <button class="btn-navy">Simpan Perubahan</button>
</form>
@endsection
