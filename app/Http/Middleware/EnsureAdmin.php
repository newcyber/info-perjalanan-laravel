<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('admin.login'));
        }

        if ($request->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses administrator.');
        }

        return $next($request);
    }
}
