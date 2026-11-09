<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) return $next($request);

        $company = $user->activeCompany();
        if (!$company) return $next($request);

        $role = $user->roleInActiveCompany();
        $isStaff = $role === 'staff';
        
        $requestedBranchId = $request->input('branch_id', $request->query('branch_id'));
        $sessionBranchId = session('branch_id');

        // JIKA BUKAN STAFF (Owner/Admin), IZINKAN AKSES KE SEMUA CABANG
        if (!$isStaff) {
            if ($requestedBranchId === 'all' || $requestedBranchId === '') {
                $request->merge(['branch_id' => null]);
            }
            return $next($request);
        }

        // --- LOGIKA DI BAWAH INI HANYA UNTUK STAFF ---
        $allowedBranches = $user->branches()->pluck('branches.id')->toArray();

        if (empty($allowedBranches)) {
            abort(403, 'Anda belum ditugaskan ke cabang manapun. Hubungi Admin.');
        }

        if ($requestedBranchId && $requestedBranchId !== 'all' && !in_array($requestedBranchId, $allowedBranches)) {
            abort(403, 'Akses ditolak: Anda tidak berhak melihat data cabang ini.');
        }

        if (!$sessionBranchId || !in_array($sessionBranchId, $allowedBranches)) {
            session(['branch_id' => $allowedBranches[0]]);
        }

        $request->merge(['branch_id' => session('branch_id')]);

        return $next($request);
    }
}