<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'SuperAdmin') {
            abort(403, 'Hanya SuperAdmin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
