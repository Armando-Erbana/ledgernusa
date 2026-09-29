<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses hanya untuk Super Admin.');
        }

        return $next($request);
    }
}