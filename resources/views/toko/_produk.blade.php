<div class="flex items-center justify-between border-b border-slate-200/80 pb-3 text-xs sm:text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
    <div>
        @if ($q !== '')
            <span>Hasil untuk “<span class="font-bold text-navy dark:text-kuning">{{ $q }}</span>” · </span>
        @endif
        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $produk->total() }} produk</span>
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3">
    @forelse ($produk as $b)
        @include('toko._kartu', ['b' => $b])
    @empty
        <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white py-16 px-4 text-center shadow-xs dark:border-slate-700 dark:bg-slate-800">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 text-amber-500 dark:bg-amber-950/40 dark:text-kuning shadow-inner">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 9v2m0 4h.01" />
                </svg>
            </div>
            <p class="mt-4 text-base font-bold text-slate-800 dark:text-white">Produk tidak ditemukan.</p>
            <p class="mt-1 max-w-sm text-xs text-slate-500 dark:text-slate-400">Barang yang Anda cari tidak ada atau belum tersedia di etalase koperasi.</p>
            <a href="{{ route('toko.belanja') }}" class="btn-navy mt-5 text-xs font-semibold">Tampilkan Semua Produk</a>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $produk->links() }}</div>
