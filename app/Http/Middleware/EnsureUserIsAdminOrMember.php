<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrMember
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        if ($request->user()->isAdmin() || $request->user()->isMember()) {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}