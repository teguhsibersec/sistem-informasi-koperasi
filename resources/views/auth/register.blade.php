@extends('layouts.auth')
@section('judul', 'Daftar')

@section('isi')
    <h1 class="text-2xl font-bold text-navy">Daftar Anggota</h1>
    <p class="mt-1 text-sm text-slate-500">Khusus siswa anggota Koperasi Sekolah SMP Negeri 2 Metro.</p>

    <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-4">
        @csrf
        @php
            $kolom = [
                ['nama', 'Nama Lengkap', 'text', 'name'],
                ['nis', 'Identitas Anggota (NIS)', 'text', 'off'],
                ['kelas', 'Kelas', 'text', 'off'],
                ['alamat', 'Alamat (opsional)', 'text', 'street-address'],
                ['kota', 'Kota (opsional)', 'text', 'address-level2'],
                ['no_hp', 'Telepon (opsional)', 'tel', 'tel'],
                ['email', 'Email', 'email', 'email'],
                ['password', 'Password (min. 8 karakter)', 'password', 'new-password'],
                ['password_confirmation', 'Ulangi Password', 'password', 'new-password'],
            ];
        @endphp
        @foreach ($kolom as [$nama, $label, $tipe, $auto])
            <div>
                <label class="label" for="{{ $nama }}">{{ $label }}</label>
                <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" autocomplete="{{ $auto }}"
                       @if (! in_array($tipe, ['password'])) value="{{ old($nama) }}" @endif
                       class="input" @if (! in_array($nama, ['no_hp', 'alamat', 'kota'])) required @endif>
                @error($nama) <p class="galat">{{ $message }}</p> @enderror
            </div>
        @endforeach
        <button class="btn-kuning w-full">Daftar</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-navy hover:underline">Masuk</a>
    </p>
@endsection
