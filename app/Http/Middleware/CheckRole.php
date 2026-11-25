<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Comprueba si el usuario autenticado posee uno de los roles permitidos.
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()?->hasRole(...$roles)) {
            abort(403);
        }

        return $next($request);
    }
}
