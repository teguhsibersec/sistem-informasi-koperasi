<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Username, NIS, atau email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /** Login boleh pakai email atau username (username anggota = NIS). */
    public function authenticate(): void
    {
        $this->pastikanTidakTerblokir();

        $kolom = filter_var($this->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$kolom => $this->input('login'), 'password' => $this->input('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->kunciThrottle());

            throw ValidationException::withMessages(['login' => 'Username/email atau password salah.']);
        }

        RateLimiter::clear($this->kunciThrottle());
    }

    protected function pastikanTidakTerblokir(): void
    {
        if (! RateLimiter::tooManyAttempts($this->kunciThrottle(), 5)) {
            return;
        }

        event(new Lockout($this));
        $detik = RateLimiter::availableIn($this->kunciThrottle());

        throw ValidationException::withMessages([
            'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
        ]);
    }

    protected function kunciThrottle(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}
