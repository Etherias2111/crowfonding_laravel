<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleCrowfondingActions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST')) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $ip = $request->ip() ?: 'unknown';

        if ($path === 'register') {
            $key = 'crowfonding:register:'.$ip;
            $maximumAttempts = 5;
            $decaySeconds = 600;
            $message = 'Has enviado demasiados registros. Inténtalo en unos minutos.';
        } elseif ($path === 'servicios') {
            $key = 'crowfonding:tickets:'.$ip;
            $maximumAttempts = 10;
            $decaySeconds = 60;
            $message = 'Has enviado demasiadas solicitudes. Espera un momento e inténtalo de nuevo.';
        } else {
            return $next($request);
        }

        if (RateLimiter::tooManyAttempts($key, $maximumAttempts)) {
            abort(Response::HTTP_TOO_MANY_REQUESTS, $message);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }
}
