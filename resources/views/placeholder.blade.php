<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judul }} - {{ config('koperasi.nama') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800">
<header class="flex items-center justify-between bg-navy px-6 py-4 text-white">
    <a href="{{ route('toko.beranda') }}" class="font-bold">{{ config('koperasi.nama') }}</a>
    @auth
        <div class="flex items-center gap-4 text-sm">
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form method="POST" action="{{ route('logout') }}">@csrf <button class="rounded bg-kuning px-3 py-1 font-semibold text-navy">Keluar</button></form>
        </div>
    @else
        <a href="{{ route('login') }}" class="rounded bg-kuning px-3 py-1 text-sm font-semibold text-navy">Masuk</a>
    @endauth
</header>
<main class="mx-auto max-w-3xl p-8">
    @if (session('sukses'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-sukses">{{ session('sukses') }}</div>
    @endif
    <h1 class="text-2xl font-bold text-navy">{{ $judul }}</h1>
    <p class="mt-2 text-slate-500">Halaman ini masih placeholder; akan diisi pada tahap berikutnya.</p>
</main>
</body>
</html>
