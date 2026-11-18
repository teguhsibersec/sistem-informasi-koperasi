<?php

namespace App\Http\Middleware;

use App\Support\Akses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Pemakaian: ->middleware('peran:admin,kasir') - lolos jika salah satu peran cocok. */
class CekPeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        abort_unless(Akses::punyaSalahSatu($request->user(), $peran), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
