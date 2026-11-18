@extends('layouts.toko')
@section('judul', 'Profil')

@section('isi')
<h1 class="text-2xl font-bold text-navy">Profil Anggota</h1>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <section class="rounded-xl bg-white p-4 text-sm shadow-sm">
        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-navy text-2xl font-bold text-white">{{ strtoupper(substr($anggota->nama_anggota, 0, 1)) }}</span>
        <p class="mt-3 text-lg font-semibold">{{ $anggota->nama_anggota }}</p>
        <dl class="mt-3 space-y-2">
            <div class="flex justify-between"><dt class="text-slate-500">NIS</dt><dd>{{ $anggota->nis_anggota }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">No. Anggota</dt><dd>{{ $anggota->no_anggota }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Kelas</dt><dd class="text-right">{{ $anggota->kelas_anggota }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Terdaftar</dt><dd>{{ $anggota->tgl_daftar_anggota->format('d/m/Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd class="font-medium text-sukses">{{ ucfirst($anggota->status_anggota) }}</dd></div>
        </dl>
        <p class="mt-3 text-xs text-slate-400">Nama, NIS, dan kelas hanya dapat diubah pengurus koperasi.</p>
    </section>

    <form method="POST" action="{{ route('toko.profil.update') }}" class="space-y-4 rounded-xl bg-white p-4 shadow-sm lg:col-span-2">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="no_hp">Telepon</label>
                <input id="no_hp" name="no_hp" value="{{ old('no_hp', $anggota->no_hp_anggota) }}" class="input" inputmode="numeric">
            </div>
            <div>
                <label class="label" for="alamat">Alamat</label>
                <input id="alamat" name="alamat" value="{{ old('alamat', $anggota->alamat_anggota) }}" maxlength="100" class="input">
            </div>
            <div>
                <label class="label" for="kota">Kota</label>
                <input id="kota" name="kota" value="{{ old('kota', $anggota->kota_anggota) }}" maxlength="35" class="input">
            </div>
        </div>

        <div class="border-t pt-4">
            <p class="font-semibold text-navy">Ganti Password <span class="text-xs font-normal text-slate-400">(kosongkan bila tidak diganti)</span></p>
            <div class="mt-3 grid gap-4 sm:grid-cols-3">
                <div><label class="label" for="password_lama">Password lama</label><input id="password_lama" type="password" name="password_lama" class="input" autocomplete="current-password"></div>
                <div><label class="label" for="password">Password baru</label><input id="password" type="password" name="password" class="input" autocomplete="new-password"></div>
                <div><label class="label" for="password_confirmation">Ulangi password baru</label><input id="password_confirmation" type="password" name="password_confirmation" class="input" autocomplete="new-password"></div>
            </div>
        </div>
        <button class="btn-navy">Simpan Perubahan</button>
    </form>
</div>
@endsection
