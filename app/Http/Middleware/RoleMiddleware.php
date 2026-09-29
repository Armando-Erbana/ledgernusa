<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Usage: ->middleware('role:owner,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $companyId = session('company_id');
        if (!$companyId) {
            return redirect()->route('companies.index');
        }

        $role = auth()->user()->companies()
            ->where('companies.id', $companyId)
            ->first()?->pivot->role;

        if (!in_array($role, $roles)) {
            abort(403, 'Akses ditolak. Role Anda: ' . ($role ?? 'tidak diketahui'));
        }

        return $next($request);
    }
}