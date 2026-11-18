@extends('layouts.admin')
@php $baru = ! $barang->exists; @endphp
@section('judul', $baru ? 'Tambah Produk' : 'Edit Produk')

@section('isi')
<a href="{{ route('admin.produk.index') }}" class="text-xs text-navy hover:underline">← Semua produk</a>
<h1 class="mt-1 text-2xl font-bold text-navy">{{ $baru ? 'Tambah Produk' : 'Edit Produk' }}</h1>

<form method="POST" enctype="multipart/form-data" class="mt-4 grid gap-4 lg:grid-cols-3"
      action="{{ $baru ? route('admin.produk.store') : route('admin.produk.update', $barang) }}">
    @csrf
    @unless ($baru) @method('PUT') @endunless

    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label" for="kode_barang">Kode *</label>
                <input id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" maxlength="10" required class="input font-mono uppercase" placeholder="ATK-001">
                @error('kode_barang') <p class="galat">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="label" for="nama_barang">Nama barang *</label>
                <input id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" maxlength="100" required class="input">
                @error('nama_barang') <p class="galat">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label" for="satuan_barang">Satuan *</label>
                <input id="satuan_barang" name="satuan_barang" value="{{ old('satuan_barang', $barang->satuan_barang) }}" maxlength="25" required class="input" placeholder="pcs / botol / rim">
                @error('satuan_barang') <p class="galat">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="id_kategori">Jenis / Kategori *</label>
                <select id="id_kategori" name="id_kategori" required class="input">
                    <option value="">Pilih kategori</option>
                    @foreach ($kategori as $k) <option value="{{ $k->id_kategori }}" @selected((string) old('id_kategori', $barang->id_kategori) === (string) $k->id_kategori)>{{ $k->nama_kategori }}</option> @endforeach
                </select>
                @error('id_kategori') <p class="galat">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="harga_jual_barang">Harga jual (Rp) *</label>
                <input id="harga_jual_barang" type="number" min="0" step="1" name="harga_jual_barang" value="{{ old('harga_jual_barang', $barang->harga_jual_barang !== null ? (int) $barang->harga_jual_barang : '') }}" required class="input">
                @error('harga_jual_barang') <p class="galat">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            @if ($baru)
                <div>
                    <label class="label" for="stok_awal">Stok awal</label>
                    <input id="stok_awal" type="number" min="0" name="stok_awal" value="{{ old('stok_awal', 0) }}" class="input">
                    @error('stok_awal') <p class="galat">{{ $message }}</p> @enderror
                </div>
            @else
                <div>
                    <p class="label">Stok saat ini</p>
                    <p class="rounded-lg bg-slate-50 px-3 py-2.5 text-sm">{{ $barang->stok_barang }} <a href="{{ route('admin.stok.edit', $barang) }}" class="ml-1 text-xs text-navy hover:underline">sesuaikan</a></p>
                </div>
            @endif
            <div>
                <label class="label" for="stok_min_barang">Stok minimum *</label>
                <input id="stok_min_barang" type="number" min="0" name="stok_min_barang" value="{{ old('stok_min_barang', $barang->stok_min_barang) }}" required class="input">
                @error('stok_min_barang') <p class="galat">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="status_barang">Status *</label>
                <select id="status_barang" name="status_barang" class="input">
                    <option value="aktif" @selected(old('status_barang', $barang->status_barang) === 'aktif')>Aktif (tampil di toko)</option>
                    <option value="nonaktif" @selected(old('status_barang', $barang->status_barang) === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div>
            <label class="label" for="deskripsi_barang">Deskripsi</label>
            <textarea id="deskripsi_barang" name="deskripsi_barang" rows="4" maxlength="2000" class="input">{{ old('deskripsi_barang', $barang->deskripsi_barang) }}</textarea>
        </div>
    </section>

    <aside class="h-fit space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="label">Gambar produk</p>
        <div class="flex aspect-square items-center justify-center overflow-hidden rounded-lg bg-slate-100">
            <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama_barang }}" class="h-full w-full object-cover">
        </div>
        <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm">
        <p class="text-xs text-slate-400">JPG/PNG/WEBP, maks. 2 MB.</p>
        @error('gambar') <p class="galat">{{ $message }}</p> @enderror
        @if ($barang->gambar_barang)
            <label class="flex items-center gap-2 text-sm text-red-600"><input type="checkbox" name="hapus_gambar" value="1" class="rounded border-slate-300"> Hapus gambar</label>
        @endif
        <button class="btn-navy w-full">{{ $baru ? 'Simpan Produk' : 'Simpan Perubahan' }}</button>
    </aside>
</form>
@endsection
