@php
    $user = auth()->user();
    $logoKoperasi = \App\Models\Pengaturan::ambil('logo_koperasi');
    $menu = \App\Support\Menu::untuk($user);
    $labelPeran = $user->isAdmin() ? 'Administrator' : ucfirst($user->pengurus?->jabatan_pengurus ?? $user->role);
@endphp
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
<body x-data="{ menu: false }" class="bg-slate-100 font-sans text-slate-800 antialiased">

<header class="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between bg-navy px-4 text-white">
    <div class="flex items-center gap-3">
        <button type="button" class="lg:hidden" @click="menu = !menu" aria-label="Menu"><x-ikon nama="menu" class="h-6 w-6" /></button>
        @if ($logoKoperasi)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($logoKoperasi) }}" alt="Logo koperasi" class="h-10 w-10 rounded-full bg-white object-contain p-0.5">
        @else
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-kuning text-xs font-bold text-navy">UM</div>
        @endif
        <div class="leading-tight">
            <p class="font-bold">{{ config('koperasi.nama') }}</p>
            <p class="hidden text-[11px] text-white/70 sm:block">{{ config('koperasi.subjudul') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-5">
        <div x-data="jamWib" class="hidden text-right text-xs leading-tight md:block">
            <p class="font-semibold" x-text="tgl"></p>
            <p class="text-white/70" x-text="jam"></p>
        </div>
        <div x-data="{ buka: false }" class="relative">
            <button type="button" @click="buka = !buka" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <span class="hidden text-left text-sm leading-tight sm:block">
                    <span class="block font-semibold">{{ $user->name }}</span>
                    <span class="block text-xs text-white/70">{{ $labelPeran }}</span>
                </span>
                <x-ikon nama="turun" class="h-4 w-4" />
            </button>
            <div x-show="buka" x-cloak @click.outside="buka = false" class="absolute right-0 mt-2 w-44 rounded-lg bg-white py-1 text-sm text-slate-700 shadow-lg">
                <a href="{{ route('toko.beranda') }}" class="block px-4 py-2 hover:bg-slate-50">Lihat Toko</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full px-4 py-2 text-left text-red-600 hover:bg-slate-50">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>

<div x-show="menu" x-cloak class="fixed inset-0 z-10 bg-black/40 lg:hidden" @click="menu = false"></div>

<aside class="fixed bottom-0 left-0 top-16 z-20 flex w-60 flex-col justify-between bg-navy pb-4 pt-4 transition-transform lg:translate-x-0"
       :class="menu ? 'translate-x-0' : '-translate-x-full'">
    <nav class="space-y-1 px-3">
        @foreach ($menu as $m)
            @php $aktif = request()->routeIs($m['aktif']); @endphp
            <a href="{{ route($m['rute']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $aktif ? 'bg-sukses text-white' : 'text-white/80 hover:bg-white/10' }}">
                <x-ikon :nama="$m['ikon']" class="h-5 w-5" /> {{ $m['label'] }}
            </a>
        @endforeach
    </nav>
    <div class="mx-4 border-l-4 border-kuning pl-3 text-xs leading-snug text-white">
        <p class="font-semibold">Bersama Koperasi</p>
        <p class="text-white/80">Membangun Kemandirian Siswa</p>
    </div>
</aside>

<main class="pt-16 lg:pl-60">
    <div class="min-h-[calc(100vh-7rem)] p-4 lg:p-6">
        @if (session('sukses'))
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-sukses">{{ session('sukses') }}</div>
        @endif
        @if (session('galat'))
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ session('galat') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ $errors->first() }}</div>
        @endif
        @yield('isi')
    </div>
    <footer class="flex flex-col justify-between gap-1 px-6 py-4 text-xs text-slate-500 sm:flex-row">
        <span>{{ config('koperasi.nama') }} &copy; 2026</span>
        <span>{{ config('koperasi.alamat') }}</span>
    </footer>
</main>
<style>[x-cloak]{display:none !important}</style>
</body>
</html>
