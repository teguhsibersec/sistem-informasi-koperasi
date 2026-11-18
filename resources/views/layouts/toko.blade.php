@php
    $user = auth()->user();
    $mhs = $user?->isAnggota() ?? false;
    $keranjangItems = $mhs
        ? \App\Models\KeranjangItem::with('barang')->whereHas('keranjang', fn ($q) => $q->where('id_anggota', $user->id_anggota))->get()
        : collect();
    $jumlahKeranjang = $keranjangItems->sum('qty');
    $tab = [
        ['Beranda', 'toko.beranda', ['toko.beranda']],
        ['Belanja', 'toko.belanja', ['toko.belanja', 'toko.produk']],
        ['Pesanan Saya', 'toko.pesanan', ['toko.pesanan', 'toko.pesanan.*']],
        ['Tentang Koperasi', 'toko.tentang', ['toko.tentang']],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'Toko') - {{ config('koperasi.nama') }}</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased selection:bg-kuning selection:text-navy dark:bg-slate-900 dark:text-slate-100 transition-colors duration-200"
      x-data="{
          scrolled: false,
          cartDrawer: false,
          mobileNav: false,
          darkMode: localStorage.getItem('theme') === 'dark',
          jumlahKeranjang: {{ (int) $jumlahKeranjang }},
          keranjangItems: {{ Js::from($keranjangItems->map(fn ($i) => [
              'id_barang' => $i->id_barang,
              'nama' => $i->barang?->nama_barang ?? 'Produk Dihapus',
              'harga' => (int) ($i->barang?->harga_jual_barang ?? 0),
              'harga_rp' => \App\Support\Rupiah::format($i->barang?->harga_jual_barang ?? 0),
              'gambar_url' => $i->barang?->gambar_url,
              'qty' => (int) $i->qty,
              'subtotal' => (int) $i->subtotal,
              'subtotal_rp' => \App\Support\Rupiah::format($i->subtotal),
          ])->values()) }},
          get totalKeranjang() {
              return this.keranjangItems.reduce((acc, i) => acc + (i.subtotal || (i.qty * i.harga)), 0);
          },
          get totalKeranjangRp() {
              return 'Rp ' + Number(this.totalKeranjang).toLocaleString('id-ID');
          },
          toast: { show: false, message: '', type: 'sukses' },
          tampilkanToast(msg, tipe = 'sukses') {
              this.toast.message = msg;
              this.toast.type = tipe;
              this.toast.show = true;
              setTimeout(() => { this.toast.show = false; }, 3500);
          },
          updateKeranjang(data) {
              if (data.jumlah !== undefined) this.jumlahKeranjang = data.jumlah;
              if (data.items !== undefined) this.keranjangItems = data.items;
          }
      }"
      x-init="
          if (darkMode) document.documentElement.classList.add('dark');
          $watch('darkMode', val => {
              if (val) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              }
          });
          window.tampilkanToast = (msg, tipe) => $data.tampilkanToast(msg, tipe);
          window.tambahKeranjang = async function(e, idBarang, qty = 1, namaBarang = '') {
              if (e && e.preventDefault) e.preventDefault();
              const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
              try {
                  const res = await fetch('{{ route('toko.keranjang.tambah') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': token
                      },
                      body: JSON.stringify({ id_barang: idBarang, qty: qty })
                  });
                  if (res.status === 401) {
                      window.location.href = '{{ route('login') }}';
                      return;
                  }
                  const data = await res.json();
                  if (res.ok && data.sukses) {
                      window.tampilkanToast((namaBarang ? namaBarang + ' ' : '') + 'berhasil ditambahkan ke keranjang!', 'sukses');
                      $data.updateKeranjang(data);
                      window.dispatchEvent(new CustomEvent('keranjang-diperbarui', { detail: data }));
                  } else {
                      window.tampilkanToast(data.pesan || data.message || 'Gagal menambahkan produk.', 'galat');
                  }
                  return data;
              } catch (err) {
                  window.tampilkanToast('Gagal menghubungi server.', 'galat');
                  throw err;
              }
          };
      "
      @scroll.window="scrolled = window.scrollY > 25">

{{-- Toast Notification --}}
<div x-show="toast.show" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed bottom-6 left-6 z-50 flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-slate-700 dark:bg-slate-800">
    <div class="flex h-8 w-8 items-center justify-center rounded-lg"
         :class="toast.type === 'sukses' ? 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400'">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    </div>
    <div>
        <p class="text-xs font-semibold text-slate-800 dark:text-white" x-text="toast.message"></p>
    </div>
    <button type="button" @click="toast.show = false" class="ml-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

{{-- Sticky Header --}}
<header class="sticky top-0 z-40 bg-navy text-white transition-all duration-300 shadow-md"
        :class="scrolled ? 'py-2 backdrop-blur-md bg-navy/95 shadow-lg' : 'py-3.5 bg-navy'">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4">
        @php $logoKoperasi = \App\Models\Pengaturan::ambil('logo_koperasi') @endphp
        <a href="{{ route('toko.beranda') }}" class="group flex items-center gap-3 transition">
            @if ($logoKoperasi)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-10 w-10 rounded-full bg-white object-contain p-0.5 shadow-sm transition group-hover:scale-105 sm:h-11 sm:w-11">
            @else
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-kuning text-xs font-bold text-navy shadow-sm transition group-hover:scale-105 sm:h-11 sm:w-11">UM</span>
            @endif
            <span class="leading-tight">
                <span class="block font-bold tracking-tight text-white transition group-hover:text-kuning">{{ config('koperasi.nama') }}</span>
                <span class="hidden text-[11px] text-white/70 sm:block">{{ config('koperasi.subjudul') }}</span>
            </span>
        </a>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('toko.belanja') }}" id="form-cari" class="order-3 flex w-full flex-1 md:order-none md:mx-6 md:w-auto md:max-w-xl">
            <div class="relative flex w-full items-center">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari perlengkapan, buku, seragam..." aria-label="Cari produk"
                       class="w-full rounded-l-full border-0 bg-white/95 px-4 py-2 text-sm text-slate-800 placeholder-slate-400 shadow-inner focus:bg-white focus:outline-none focus:ring-2 focus:ring-kuning dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400"
                       @input="window.cariProduk($event.target.value)">
                <button class="flex h-full items-center rounded-r-full bg-kuning px-4 font-semibold text-navy transition hover:bg-yellow-400" aria-label="Cari">
                    <x-ikon nama="cari" class="h-5 w-5" />
                </button>
            </div>
        </form>

        {{-- Actions --}}
        <div class="ml-auto flex items-center gap-3 sm:gap-4">
            {{-- Dark Mode Toggle --}}
            <button type="button" @click="darkMode = !darkMode" class="rounded-full p-2 text-white/80 transition hover:bg-white/10 hover:text-white" :title="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'" aria-label="Toggle dark mode">
                <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="darkMode" x-cloak class="h-5 w-5 text-kuning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>

            {{-- Keranjang Trigger --}}
            <div class="relative">
                <button type="button" @click="cartDrawer = !cartDrawer" class="relative flex items-center rounded-full p-2 text-white/90 transition hover:bg-white/10 hover:text-white" aria-label="Buka Keranjang">
                    <x-ikon nama="keranjang" class="h-6 w-6" />
                    <span x-show="jumlahKeranjang > 0" x-cloak
                          x-text="jumlahKeranjang"
                          class="absolute -right-1 -top-1 flex h-5 min-w-5 animate-pulse items-center justify-center rounded-full bg-kuning px-1 text-[11px] font-bold text-navy shadow-sm">
                    </span>
                </button>
            </div>

            {{-- User Account Dropdown --}}
            @auth
                <div x-data="{ buka: false }" class="relative">
                    <button type="button" @click="buka = !buka" class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-sm transition hover:bg-white/10">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-kuning font-bold text-navy text-xs">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="hidden max-w-[120px] truncate sm:inline text-xs font-semibold">{{ $mhs ? $user->name : 'Staff / Admin' }}</span>
                        <x-ikon nama="turun" class="h-4 w-4 text-white/70" />
                    </button>
                    <div x-show="buka" x-cloak @click.outside="buka = false"
                         class="absolute right-0 z-50 mt-2 w-52 rounded-xl border border-slate-100 bg-white py-1.5 text-sm text-slate-700 shadow-xl dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        @if ($mhs)
                            <div class="border-b border-slate-100 px-4 py-2.5 dark:border-slate-700">
                                <p class="text-xs font-semibold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Anggota Koperasi</p>
                            </div>
                            <a href="{{ route('toko.profil') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <a href="{{ route('toko.pesanan') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Pesanan Saya
                            </a>
                        @else
                            <a href="{{ \App\Support\HalamanAwal::untuk($user) }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-navy dark:text-kuning font-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Ke Dashboard Admin
                            </a>
                        @endif
                        <div class="my-1 border-t border-slate-100 dark:border-slate-700"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2 text-sm">
                    <a href="{{ route('login') }}" class="rounded-full border border-white/40 px-3.5 py-1.5 text-xs font-semibold transition hover:bg-white/10 sm:text-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="hidden rounded-full bg-kuning px-4 py-1.5 text-xs font-semibold text-navy transition hover:bg-yellow-400 sm:inline sm:text-sm">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</header>

{{-- Navigation Tabs --}}
<div class="mx-auto max-w-7xl px-4 pt-4">
    <nav class="flex items-center justify-between overflow-x-auto rounded-2xl bg-white p-1.5 shadow-sm dark:bg-slate-800 dark:shadow-none border border-slate-200/80 dark:border-slate-700">
        <div class="flex items-center gap-1.5">
            @foreach ($tab as [$label, $rute, $pola])
                @php $aktif = request()->routeIs(...$pola); @endphp
                <a href="{{ route($rute) }}"
                   class="whitespace-nowrap rounded-xl px-4 py-2 text-xs sm:text-sm font-semibold transition-all duration-200 {{ $aktif ? 'bg-navy text-white shadow-sm dark:bg-blue-600' : 'text-slate-600 hover:bg-slate-100 hover:text-navy dark:text-slate-300 dark:hover:bg-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <div class="hidden items-center gap-2 pr-2 text-xs text-slate-500 dark:text-slate-400 md:flex">
            <span class="inline-block h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
            <span>Koperasi Buka & Melayani</span>
        </div>
    </nav>
</div>

{{-- Main Content --}}
<main class="mx-auto min-h-[60vh] max-w-7xl px-4 py-4">
    @if (session('sukses'))
        <div class="mb-4 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-3.5 text-sm font-medium text-sukses dark:border-green-800 dark:bg-green-950/40 dark:text-green-300">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
        </div>
    @endif
    @if (session('galat'))
        <div class="mb-4 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm font-medium text-red-600 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('galat') }}</span>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm font-medium text-red-600 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    @yield('isi')
</main>

{{-- Mini Cart Drawer (Slide-in dari kanan) --}}
<div x-show="cartDrawer" x-cloak
     class="fixed inset-0 z-50 overflow-hidden"
     aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div x-show="cartDrawer"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
         @click="cartDrawer = false"></div>

    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div x-show="cartDrawer"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md bg-white shadow-2xl dark:bg-slate-800 flex flex-col">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    <x-ikon nama="keranjang" class="h-5 w-5 text-navy dark:text-kuning" />
                    <h2 class="text-base font-bold text-navy dark:text-white">Keranjang Belanja</h2>
                    <span class="rounded-full bg-navy/10 px-2 py-0.5 text-xs font-semibold text-navy dark:bg-blue-900/50 dark:text-blue-300">
                        <span x-text="jumlahKeranjang"></span> item
                    </span>
                </div>
                <button type="button" @click="cartDrawer = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                <template x-if="keranjangItems.length > 0">
                    <div class="space-y-3">
                        <template x-for="item in keranjangItems" :key="item.id_barang">
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 shadow-2xs dark:border-slate-700 dark:bg-slate-800/80">
                                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-700">
                                    <template x-if="item.gambar_url">
                                        <img :src="item.gambar_url" :alt="item.nama" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!item.gambar_url">
                                        <div class="flex h-full w-full items-center justify-center text-xs font-bold text-slate-400">?</div>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-xs font-semibold text-slate-800 dark:text-white" x-text="item.nama"></h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="item.qty + ' x ' + (item.harga_rp || ('Rp ' + Number(item.harga).toLocaleString('id-ID')))"></p>
                                    <p class="text-xs font-bold text-navy dark:text-kuning" x-text="item.subtotal_rp || ('Rp ' + Number(item.subtotal || item.qty * item.harga).toLocaleString('id-ID'))"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="keranjangItems.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-700">
                            <x-ikon nama="keranjang" class="h-8 w-8" />
                        </div>
                        <p class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Keranjang masih kosong</p>
                        <p class="mt-1 text-xs text-slate-400">Jelajahi etalase dan temukan kebutuhan sekolah Anda.</p>
                        <a href="{{ route('toko.belanja') }}" @click="cartDrawer = false" class="btn-navy mt-4 text-xs">Mulai Belanja</a>
                    </div>
                </template>
            </div>

            <template x-if="keranjangItems.length > 0">
                <div class="border-t border-slate-100 p-6 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Total Belanja:</span>
                        <span class="text-lg font-bold text-navy dark:text-kuning" x-text="totalKeranjangRp"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('toko.keranjang') }}" class="btn border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-white">Lihat Keranjang</a>
                        <a href="{{ route('toko.checkout') }}" class="btn-kuning text-xs font-semibold text-navy">Checkout</a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Floating WhatsApp & Back-to-Top Buttons --}}
<div class="fixed bottom-6 right-6 z-40 flex flex-col items-center gap-3">
    {{-- Back to Top Button --}}
    <button type="button" x-show="scrolled" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-90"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-navy shadow-lg transition-transform hover:-translate-y-1 hover:bg-slate-50 dark:bg-slate-800 dark:text-white dark:border dark:border-slate-700"
            aria-label="Kembali ke atas">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    {{-- Floating WhatsApp --}}
    <a href="{{ \App\Models\Pengaturan::noAdminWaLink('Halo Admin Koperasi Sekolah SMP Negeri 2 Metro') }}"
       target="_blank" rel="noopener noreferrer"
       class="group relative flex h-13 w-13 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl transition-all duration-300 hover:scale-105 hover:bg-emerald-600"
       aria-label="Hubungi WhatsApp Koperasi">
        <span class="absolute -left-36 top-1/2 -translate-y-1/2 rounded-lg bg-slate-900/90 px-3 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 pointer-events-none">
            Tanya Koperasi (WA)
        </span>
        <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.662 1.435 5.183L2 22l4.954-1.399C8.423 21.493 10.15 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
        </svg>
    </a>
</div>

{{-- Footer --}}
<footer class="mt-12 border-t border-slate-200 bg-navy text-xs text-white/80 dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 font-bold text-white text-sm">
                    @if ($logoKoperasi)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-7 w-7 rounded-full bg-white object-contain p-0.5 shadow-xs">
                    @else
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-kuning text-[10px] text-navy">UM</span>
                    @endif
                    {{ config('koperasi.nama') }}
                </div>
                <p class="mt-3 text-white/70 leading-relaxed">{{ config('koperasi.subjudul') }}. Menyediakan perlengkapan sekolah terpercaya, aman, dan transparan.</p>
            </div>
            <div>
                <p class="font-bold text-white text-sm">Tautan Cepat</p>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('toko.beranda') }}" class="hover:text-kuning transition">Beranda Toko</a></li>
                    <li><a href="{{ route('toko.belanja') }}" class="hover:text-kuning transition">Katalog Belanja</a></li>
                    <li><a href="{{ route('toko.pesanan') }}" class="hover:text-kuning transition">Pesanan Saya</a></li>
                    <li><a href="{{ route('toko.tentang') }}" class="hover:text-kuning transition">Tentang Koperasi</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white text-sm">Layanan Anggota</p>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('login') }}" class="hover:text-kuning transition">Masuk Akun Anggota</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-kuning transition">Daftar Anggota Baru</a></li>
                    <li><a href="{{ route('toko.keranjang') }}" class="hover:text-kuning transition">Keranjang Belanja</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white text-sm">Alamat & Kontak</p>
                <p class="mt-3 text-white/70 leading-relaxed">{{ config('koperasi.alamat') }}</p>
                <p class="mt-2 text-white/70">WhatsApp: <span class="text-kuning font-semibold">{{ \App\Models\Pengaturan::noAdmin() }}</span></p>
            </div>
        </div>
        <div class="mt-8 border-t border-white/10 pt-4 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>{{ config('koperasi.nama') }} &copy; 2026. Hak Cipta Dilindungi.</span>
            <span>Aplikasi Koperasi Sekolah Terpadu</span>
        </div>
    </div>
</footer>

<style>[x-cloak]{display:none !important}</style>
</body>
</html>
