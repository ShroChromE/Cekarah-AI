<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Restrict a route to authenticated users holding one of the given roles.
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,volunteer').
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Autentikasi relawan diperlukan.');
        }

        // Admin may access any volunteer-scoped route.
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke Portal Relawan ini.');
        }

        return $next($request);
    }
}
