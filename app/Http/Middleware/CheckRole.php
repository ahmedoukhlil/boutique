<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = ''): Response
    {
        $user = $request->user();
        if (!$user || !$user->actif) {
            abort(403, 'Compte désactivé.');
        }
        if ($permission && !$user->hasPermission($permission)) {
            abort(403, 'Accès non autorisé.');
        }
        return $next($request);
    }
}
