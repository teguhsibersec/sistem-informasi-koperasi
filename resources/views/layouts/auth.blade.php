<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul') - {{ config('koperasi.nama') }}</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased selection:bg-kuning selection:text-navy">
<div class="grid min-h-screen lg:grid-cols-2">
    @hasSection('panel-kiri')
        @yield('panel-kiri')
    @else
        @php $logoKoperasi = \App\Models\Pengaturan::ambil('logo_koperasi') @endphp
        <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-[#07154a] via-navy to-[#122a85] p-10 text-white lg:flex select-none"
             x-data="{
                 mx: 0,
                 my: 0,
                 activeText: 0,
                 texts: ['lebih mudah', 'lebih cepat', 'lebih hemat']
             }"
             x-init="setInterval(() => { activeText = (activeText + 1) % texts.length }, 3000)"
             @mousemove.window="if (window.innerWidth >= 1024) { mx = ($event.clientX / window.innerWidth - 0.5) * 18; my = ($event.clientY / window.innerHeight - 0.5) * 18; }">

            {{-- 1. Animated Gradient Blobs --}}
            <div class="pointer-events-none absolute -left-20 -top-20 h-80 w-80 rounded-full bg-kuning/15 blur-3xl animate-blob-1"
                 :style="{ transform: 'translate(' + (mx * 0.4) + 'px, ' + (my * 0.4) + 'px)' }"></div>
            <div class="pointer-events-none absolute -right-24 top-1/2 h-96 w-96 -translate-y-1/2 rounded-full bg-sky-400/20 blur-3xl animate-blob-2"
                 :style="{ transform: 'translate(' + (-mx * 0.3) + 'px, ' + (-my * 0.3) + 'px)' }"></div>
            <div class="pointer-events-none absolute -bottom-24 left-1/3 h-80 w-80 rounded-full bg-blue-400/15 blur-3xl animate-blob-3"
                 :style="{ transform: 'translate(' + (mx * 0.2) + 'px, ' + (my * 0.2) + 'px)' }"></div>

            {{-- 2. Subtle Dot Grid Texture --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.07] [background-image:radial-gradient(rgba(255,255,255,0.9)_1.25px,transparent_1.25px)] [background-size:24px_24px]"></div>

            {{-- 3. Floating School-Themed Icons --}}
            <div class="pointer-events-none absolute inset-0" :style="{ transform: 'translate(' + (mx * 0.5) + 'px, ' + (my * 0.5) + 'px)' }">
                {{-- Buku (Book) --}}
                <div class="absolute top-12 right-16 text-white/15 animate-float-1" aria-hidden="true">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>

                {{-- Pensil (Pencil) --}}
                <div class="absolute top-[38%] right-10 text-kuning/20 animate-float-2" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                </div>

                {{-- Penggaris (Ruler) --}}
                <div class="absolute bottom-28 right-16 text-white/15 animate-float-3" aria-hidden="true">
                    <svg class="h-11 w-11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75l16.5 16.5m-3.75-12.75l1.5 1.5m-3 0l1.5 1.5m-3 0l1.5 1.5m-3 0l1.5 1.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M3 21l3-18 15 15z" />
                    </svg>
                </div>

                {{-- Tas Sekolah (Backpack) --}}
                <div class="absolute top-28 left-64 text-white/15 animate-float-4" aria-hidden="true">
                    <svg class="h-11 w-11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 10.5A2.25 2.25 0 018.25 8.25h7.5A2.25 2.25 0 0118 10.5v8.25A3.75 3.75 0 0114.25 22.5h-4.5A3.75 3.75 0 016 18.75V10.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 12h4.5m-4.5 3.75h4.5" />
                    </svg>
                </div>

                {{-- Keranjang Belanja (Shopping Basket) --}}
                <div class="absolute top-[62%] left-10 text-white/15 animate-float-5" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>

                {{-- Koin (Coin) --}}
                <div class="absolute bottom-16 left-1/3 text-kuning/20 animate-float-6" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v10m-3-6.5h4.5a1.5 1.5 0 010 3H10a1.5 1.5 0 000 3H15" />
                    </svg>
                </div>
            </div>

            {{-- 5. Fade-In Step 1: Logo Header --}}
            <div class="fade-step-1 relative z-10 flex items-center gap-3">
                @if ($logoKoperasi)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-12 w-12 rounded-full bg-white object-contain p-0.5 shadow-md">
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-kuning text-sm font-bold text-navy shadow-md">UM</div>
                @endif
                <div>
                    <p class="text-lg font-bold tracking-tight">{{ config('koperasi.nama') }}</p>
                    <p class="text-xs text-white/70">{{ config('koperasi.subjudul') }}</p>
                </div>
            </div>

            {{-- 5. Fade-In Step 2 & 3: Headline & Description + Step 4: Glassmorphism Stats --}}
            <div class="relative z-10 my-auto py-8">
                <div class="fade-step-2">
                    {{-- 4. Rotating Text pada Headline --}}
                    <h2 class="text-4xl font-bold leading-tight">
                        Belanja kebutuhan<br>sekolah 
                        <span class="relative inline-grid text-kuning">
                            <span class="col-start-1 row-start-1 transition-all duration-500 ease-out"
                                  :class="activeText === 0 ? 'opacity-100 translate-y-0 filter-none' : 'opacity-0 -translate-y-2 pointer-events-none blur-[1px]'">
                                lebih mudah
                            </span>
                            <span class="col-start-1 row-start-1 transition-all duration-500 ease-out"
                                  :class="activeText === 1 ? 'opacity-100 translate-y-0 filter-none' : 'opacity-0 -translate-y-2 pointer-events-none blur-[1px]'">
                                lebih cepat
                            </span>
                            <span class="col-start-1 row-start-1 transition-all duration-500 ease-out"
                                  :class="activeText === 2 ? 'opacity-100 translate-y-0 filter-none' : 'opacity-0 -translate-y-2 pointer-events-none blur-[1px]'">
                                lebih hemat
                            </span>
                        </span>
                    </h2>
                </div>

                <p class="fade-step-3 mt-4 max-w-md text-white/80 leading-relaxed">
                    Alat tulis, makanan, hingga perlengkapan sekolah dalam satu tempat.
                </p>

                {{-- 6. Glassmorphism Stat Cards --}}
                <div class="fade-step-4 mt-8 grid grid-cols-3 gap-3">
                    <div class="animate-card-1 rounded-xl border border-white/15 bg-white/10 p-3 shadow-lg backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-kuning/20 text-kuning">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs font-semibold text-white">Ribuan Produk</p>
                        <p class="text-[10px] text-white/70">Kebutuhan lengkap</p>
                    </div>

                    <div class="animate-card-2 rounded-xl border border-white/15 bg-white/10 p-3 shadow-lg backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-400/20 text-sky-300">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs font-semibold text-white">Transaksi Cepat</p>
                        <p class="text-[10px] text-white/70">POS & QRIS instan</p>
                    </div>

                    <div class="animate-card-3 rounded-xl border border-white/15 bg-white/10 p-3 shadow-lg backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-400/20 text-emerald-300">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs font-semibold text-white">Aman & Tercatat</p>
                        <p class="text-[10px] text-white/70">Stok transparan</p>
                    </div>
                </div>
            </div>

            {{-- 5. Fade-In Step 5: Footer --}}
            <p class="fade-step-5 relative z-10 text-xs text-white/60">
                {{ config('koperasi.nama') }} &copy; 2026 | {{ config('koperasi.alamat') }}
            </p>
        </div>
    @endif

    <div class="flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="mb-6 flex items-center gap-3 lg:hidden">
                @if ($logoKoperasi)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-10 w-10 rounded-full bg-white object-contain p-0.5 shadow-sm">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-navy text-xs font-bold text-kuning shadow-sm">UM</div>
                @endif
                <p class="font-bold text-navy">{{ config('koperasi.nama') }}</p>
            </div>
            @yield('isi')
        </div>
    </div>
</div>
</body>
</html>
