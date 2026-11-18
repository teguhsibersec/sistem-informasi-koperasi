@extends('layouts.admin')
@section('judul', 'Catat Pembelian')

@section('isi')
<a href="{{ route('admin.pembelian.index') }}" class="text-xs text-navy hover:underline">← Semua pembelian</a>
<h1 class="mt-1 text-2xl font-bold text-navy">Catat Pembelian</h1>

<form method="POST" action="{{ route('admin.pembelian.store') }}" class="mt-4 space-y-4" x-data="formPembelian(@js($barang), @js(old('items', [])))">
    @csrf
    <section class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
        <div>
            <label class="label" for="id_pemasok">Pemasok *</label>
            <select id="id_pemasok" name="id_pemasok" required class="input">
                <option value="">Pilih pemasok</option>
                @foreach ($pemasok as $p) <option value="{{ $p->id_pemasok }}" @selected((string) old('id_pemasok') === (string) $p->id_pemasok)>{{ $p->nama_pemasok }}</option> @endforeach
            </select>
            @if ($pemasok->isEmpty()) <p class="mt-1 text-xs text-red-600">Belum ada pemasok. <a href="{{ route('admin.pembelian.pemasok.index') }}" class="underline">Tambah dulu</a>.</p> @endif
        </div>
        <div><label class="label" for="no_faktur_pembelian">No. Faktur *</label><input id="no_faktur_pembelian" name="no_faktur_pembelian" value="{{ old('no_faktur_pembelian', $faktur) }}" maxlength="20" required class="input font-mono"></div>
        <div><label class="label" for="tgl_pembelian">Tanggal *</label><input id="tgl_pembelian" type="date" name="tgl_pembelian" value="{{ old('tgl_pembelian', now()->toDateString()) }}" required class="input"></div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-semibold text-navy">Barang</h2>
        <div class="mt-3 space-y-2">
            <template x-for="(b, i) in baris" :key="i">
                <div class="grid items-center gap-2 sm:grid-cols-12">
                    <select :name="`items[${i}][id_barang]`" x-model="b.id_barang" required class="input sm:col-span-5">
                        <option value="">Pilih barang</option>
                        <template x-for="x in barang" :key="x.id"><option :value="x.id" x-text="x.label"></option></template>
                    </select>
                    <input type="number" min="1" :name="`items[${i}][qty]`" x-model.number="b.qty" required class="input sm:col-span-2" placeholder="Jumlah">
                    <input type="number" min="0" step="1" :name="`items[${i}][harga]`" x-model.number="b.harga" required class="input sm:col-span-2" placeholder="Harga beli">
                    <p class="text-right text-sm font-medium sm:col-span-2" x-text="rp((Number(b.qty) || 0) * (Number(b.harga) || 0))"></p>
                    <button type="button" @click="hapus(i)" class="text-red-500 sm:col-span-1" aria-label="Hapus baris">✕</button>
                </div>
            </template>
        </div>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <button type="button" @click="tambah()" class="rounded-lg border px-3 py-1.5 text-sm font-medium hover:border-navy">+ Tambah baris</button>
            <p class="text-sm">Total: <span class="text-xl font-bold text-navy" x-text="rp(total)"></span></p>
        </div>
    </section>

    <button class="btn-navy">Simpan (status Dipesan)</button>
</form>
@endsection
