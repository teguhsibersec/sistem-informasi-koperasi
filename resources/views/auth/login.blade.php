@extends('layouts.auth')
@section('judul', 'Masuk')

@section('isi')
<div class="animate-slide-up-fade">
    <h1 class="text-2xl font-bold text-navy">Masuk</h1>
    <p class="mt-1 text-sm text-slate-500">Gunakan username, NIS, atau email Anda.</p>

    @if (session('sukses'))
        <div class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-sukses">{{ session('sukses') }}</div>
    @endif

    @if (app()->environment('local'))
        <div class="mt-5 rounded-lg border border-slate-200 bg-white p-4 shadow-xs transition hover:border-slate-300">
            <p class="text-sm font-semibold text-slate-700">Login cepat akun demo</p>
            <p class="mt-1 text-xs text-slate-500">Pilih peran untuk mengisi username dan password.</p>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <button type="button" data-login-cepat="admin" aria-pressed="false" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-navy transition-all duration-200 hover:border-navy/50 hover:bg-navy/5 hover:text-navy active:scale-95 shadow-xs">Administrator</button>
                <button type="button" data-login-cepat="kasir" aria-pressed="false" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-navy transition-all duration-200 hover:border-navy/50 hover:bg-navy/5 hover:text-navy active:scale-95 shadow-xs">Kasir (Bendahara)</button>
                <button type="button" data-login-cepat="gudang" aria-pressed="false" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-navy transition-all duration-200 hover:border-navy/50 hover:bg-navy/5 hover:text-navy active:scale-95 shadow-xs">Gudang (Sekretaris)</button>
                <button type="button" data-login-cepat="anggota" aria-pressed="false" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-navy transition-all duration-200 hover:border-navy/50 hover:bg-navy/5 hover:text-navy active:scale-95 shadow-xs">Anggota</button>
            </div>
        </div>
        <script>
            const tombolLoginCepat = document.querySelectorAll('[data-login-cepat]');
            tombolLoginCepat.forEach((tombol) => {
                tombol.addEventListener('click', () => {
                    tombolLoginCepat.forEach((pilihan) => {
                        pilihan.classList.remove('bg-navy', 'text-white', 'border-navy', 'shadow-md');
                        pilihan.classList.add('bg-white', 'text-navy', 'border-slate-200');
                        pilihan.setAttribute('aria-pressed', 'false');
                    });
                    tombol.classList.remove('bg-white', 'text-navy', 'border-slate-200');
                    tombol.classList.add('bg-navy', 'text-white', 'border-navy', 'shadow-md');
                    tombol.setAttribute('aria-pressed', 'true');
                    document.getElementById('login').value = tombol.dataset.loginCepat;
                    document.getElementById('password').value = 'password';
                });
            });
        </script>
    @endif

    <form method="POST" action="{{ url('/login') }}"
          class="mt-6 space-y-4 {{ $errors->any() ? 'animate-shake' : '' }}"
          x-data="{ isSubmitting: false, showPassword: false }"
          @submit="isSubmitting = true">
        @csrf
        <div>
            <label class="label" for="login">Username / NIS / Email</label>
            <input id="login" name="login" value="{{ old('login') }}" class="input" required autofocus autocomplete="username">
            @error('login') <p class="galat">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="password">Password</label>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" type="password" name="password" class="input pr-10" required autocomplete="current-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-navy focus:outline-none transition" aria-label="Lihat password" tabindex="-1">
                    {{-- Eye Open --}}
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{-- Eye Slash --}}
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password') <p class="galat">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-navy focus:ring-navy/30"> Ingat saya
        </label>
        <button class="btn-navy w-full relative transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-navy/25 active:translate-y-0 active:scale-[0.98] disabled:opacity-85 disabled:cursor-not-allowed" :disabled="isSubmitting">
            <span class="inline-flex items-center justify-center gap-2">
                <svg x-show="isSubmitting" x-cloak class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Memproses...' : 'Masuk'">Masuk</span>
            </span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-navy hover:underline">Daftar sebagai anggota</a>
    </p>
</div>
@endsection
