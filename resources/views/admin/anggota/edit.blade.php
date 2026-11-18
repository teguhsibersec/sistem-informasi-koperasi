@extends('layouts.admin')
@section('judul', 'Edit Anggota')

@section('isi')
<a href="{{ route('admin.anggota.index') }}" class="text-xs text-navy hover:underline">← Data Anggota</a>
<h1 class="mt-1 text-2xl font-bold text-navy">Edit Anggota <span class="font-mono text-base text-slate-400">{{ $anggota->no_anggota }}</span></h1>

<form method="POST" action="{{ route('admin.anggota.update', $anggota) }}" class="mt-4 space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    @csrf @method('PUT')
    @include('admin.anggota._form', ['a' => $anggota])
    <button class="btn-navy">Simpan Perubahan</button>
</form>
@endsection
