<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $companyId = session('company_id');
            if (!$companyId) {
                $first = auth()->user()->companies()->first();
                if ($first) {
                    session(['company_id' => $first->id]);
                }
            }
        }
        return $next($request);
    }
}