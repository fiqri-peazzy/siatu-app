<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Hanya akun yang terhubung ke data Tenaga Kependidikan (users ↔ master_tendiks). */
class PastikanTendik
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->masterTendik, 403, 'Halaman ini khusus tenaga kependidikan.');

        return $next($request);
    }
}
