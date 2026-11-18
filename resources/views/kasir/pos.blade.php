@extends('layouts.admin')
@section('judul', 'Kasir (POS)')

@section('isi')
<h1 class="text-2xl font-bold text-navy">Kasir (POS)</h1>
<p class="text-sm text-slate-500">Cari atau scan barang, pilih anggota (opsional), lalu pilih metode pembayaran.</p>

<div class="mt-4 grid gap-4 xl:grid-cols-5"
     x-data="posKasir(@js(['urlBarang' => route('kasir.barang'), 'urlAnggota' => route('kasir.anggota'), 'urlCheckout' => route('kasir.checkout')]))">

    {{-- Daftar barang --}}
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm xl:col-span-3">
        <input type="text" x-model="q" @input.debounce.250ms="cari()" @keydown.enter.prevent="enterCari()"
               placeholder="Cari / scan kode atau nama barang..." class="input" autofocus>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <template x-for="b in hasil" :key="b.id_barang">
                <button type="button" @click="tambah(b)" :disabled="b.stok <= 0"
                        class="rounded-lg border border-slate-200 p-3 text-left transition hover:border-navy disabled:cursor-not-allowed disabled:opacity-40">
                    <p class="text-xs text-slate-400" x-text="b.kode_barang"></p>
                    <p class="text-sm font-medium" x-text="b.nama_barang"></p>
                    <p class="mt-1 text-sm font-semibold text-navy" x-text="rp(b.harga)"></p>
                    <p class="text-xs" :class="b.stok <= 5 ? 'text-red-600' : 'text-slate-500'" x-text="b.stok > 0 ? 'Stok: ' + b.stok : 'Stok habis'"></p>
                </button>
            </template>
            <p x-show="hasil.length === 0" class="col-span-full py-6 text-center text-sm text-slate-400">Barang tidak ditemukan.</p>
        </div>
    </section>

    {{-- Keranjang & pembayaran --}}
    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm xl:col-span-2">
        <div>
            <p class="label">Pembeli</p>
            <template x-if="anggota">
                <div class="flex items-center justify-between rounded-lg bg-slate-50 p-2 text-sm">
                    <span><span class="font-medium" x-text="anggota.nama_anggota"></span> <span class="text-slate-400" x-text="'· ' + anggota.nis_anggota"></span></span>
                    <button type="button" class="text-xs text-red-600" @click="anggota = null">Ganti</button>
                </div>
            </template>
            <div x-show="!anggota" class="relative">
                <input type="text" x-model="qa" @input.debounce.300ms="cariAnggota()" placeholder="Umum. Cari NIS / No. Anggota / nama..." class="input">
                <div x-show="anggotaHasil.length" class="absolute z-10 mt-1 w-full rounded-lg border bg-white shadow-lg">
                    <template x-for="a in anggotaHasil" :key="a.id_anggota">
                        <button type="button" @click="pilihAnggota(a)" class="block w-full px-3 py-2 text-left text-sm hover:bg-slate-50">
                            <span x-text="a.nama_anggota"></span> <span class="text-xs text-slate-400" x-text="a.nis_anggota + ' · ' + a.no_anggota"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div>
            <p class="label">Keranjang</p>
            <div class="divide-y">
                <template x-for="it in keranjang" :key="it.id_barang">
                    <div class="flex items-center gap-2 py-2 text-sm">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium" x-text="it.nama"></p>
                            <p class="text-xs text-slate-500" x-text="rp(it.harga)"></p>
                        </div>
                        <button type="button" class="h-7 w-7 rounded border" @click="ubah(it, -1)">-</button>
                        <input type="number" x-model.number="it.qty" @change="setQty(it)" min="1" class="h-7 w-12 rounded border text-center">
                        <button type="button" class="h-7 w-7 rounded border" @click="ubah(it, 1)">+</button>
                        <p class="w-24 text-right font-medium" x-text="rp(it.qty * it.harga)"></p>
                        <button type="button" class="text-red-500" @click="hapus(it)" aria-label="Hapus">✕</button>
                    </div>
                </template>
                <p x-show="keranjang.length === 0" class="py-6 text-center text-sm text-slate-400">Belum ada barang.</p>
            </div>
        </div>

        <div class="flex items-center justify-between border-t pt-3">
            <span class="font-semibold">Total</span>
            <span class="text-2xl font-bold text-navy" x-text="rp(total)"></span>
        </div>

        <div>
            <p class="label">Metode Bayar</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="metode = 'cash'" :class="metode === 'cash' ? 'bg-navy text-white' : 'border bg-white text-slate-700'" class="rounded-lg px-3 py-2 text-sm font-semibold">Cash</button>
                <button type="button" @click="metode = 'qris'" :class="metode === 'qris' ? 'bg-navy text-white' : 'border bg-white text-slate-700'" class="rounded-lg px-3 py-2 text-sm font-semibold">QRIS</button>
            </div>
        </div>

        <div x-show="metode === 'cash'" class="space-y-2">
            <label class="label" for="uang">Uang diterima</label>
            <input id="uang" type="number" min="0" x-model.number="uang" class="input" placeholder="0">
            <div class="flex flex-wrap gap-2">
                <template x-for="n in cepatNominal" :key="n">
                    <button type="button" @click="uang = n" class="rounded-lg border px-3 py-1.5 text-xs font-medium hover:border-navy" x-text="rp(n)"></button>
                </template>
                <button type="button" @click="uang = total" class="rounded-lg border border-sukses px-3 py-1.5 text-xs font-medium text-sukses">Uang Pas</button>
            </div>
            <p class="text-sm">Kembalian: <span class="font-bold" x-text="rp(kembalian)"></span></p>
            <p x-show="uang !== '' && Number(uang) < total" class="text-xs text-red-600">Uang diterima kurang dari total.</p>
        </div>
        <p x-show="metode === 'qris'" class="rounded-lg bg-slate-50 p-3 text-xs text-slate-600">QR akan ditampilkan setelah transaksi diproses. Stok langsung dicadangkan dan dilepas bila QRIS kedaluwarsa.</p>

        <p x-show="galat" x-text="galat" class="rounded-lg bg-red-50 p-3 text-sm text-red-600"></p>

        <button type="button" @click="bayar()" :disabled="!bisaProses" class="btn-kuning w-full py-3 disabled:cursor-not-allowed disabled:opacity-50">
            <span x-text="proses ? 'Memproses...' : (metode === 'qris' ? 'Buat QRIS' : 'Bayar & Cetak Nota')"></span>
        </button>
    </section>
</div>
@endsection
