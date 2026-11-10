<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

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

        // JIKA BUKAN STAFF (Owner/Admin), IZINKAN SEMUA AKSES
        if (!$isStaff) {
            if ($requestedBranchId === 'all' || $requestedBranchId === '') {
                $request->merge(['branch_id' => null]);
            }
            return $next($request);
        }

        // --- LOGIKA HANYA UNTUK STAFF ---
        $allowedBranches = $user->branches()->pluck('branches.id')->toArray();

        // JIKA STAFF BELUM PUNYA CABANG
        if (empty($allowedBranches)) {
            try {
                // Coba cari atau buat cabang utama
                $mainBranch = \App\Models\Branch::where('company_id', $company->id)->first();
                
                if (!$mainBranch) {
                    $mainBranch = \App\Models\Branch::create([
                        'company_id' => $company->id,
                        'name' => 'Cabang Pusat',
                        'is_main' => true,
                    ]);
                }
                
                $user->branches()->syncWithoutDetaching([$mainBranch->id]);
                $allowedBranches = [$mainBranch->id];
                
            } catch (\Exception $e) {
                // JIKA GAGAL MEMBUAT CABANG (Error DB), JANGAN BLOKIR USER!
                Log::warning("Gagal auto-create cabang: " . $e->getMessage());
                
                // Biarkan user masuk dengan mode tanpa cabang (null)
                $request->merge(['branch_id' => null]);
                return $next($request);
            }
        }

        // Jika staff mencoba akses cabang lain via URL, blokir!
        if ($requestedBranchId && $requestedBranchId !== 'all' && !in_array($requestedBranchId, $allowedBranches)) {
            abort(403, 'Akses ditolak: Anda tidak berhak melihat data cabang ini.');
        }

        // Set session
        if (!$sessionBranchId || !in_array($sessionBranchId, $allowedBranches)) {
            session(['branch_id' => $allowedBranches[0] ?? null]);
        }

        $request->merge(['branch_id' => session('branch_id')]);

        return $next($request);
    }
}