<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user?->role === 'admin', 403);

        if (app()->isProduction() && ! $user->hasEnabledTwoFactorAuthentication()) {
            return redirect()
                ->route('security.edit')
                ->with('admin_two_factor_required', true);
        }

        return $next($request);
    }
}
