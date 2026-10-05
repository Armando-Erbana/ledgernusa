<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id');

        $query = AuditLog::where('company_id', $companyId)
            ->with('user')
            ->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->paginate(30)->withQueryString();

        $users = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('companies.id', $companyId);
        })->orderBy('name')->get();

        $tables = AuditLog::where('company_id', $companyId)
            ->distinct()->pluck('table_name')->filter()->sort()->values();

        return view('audit-logs.index', compact('logs', 'users', 'tables'));
    }

    public function show(AuditLog $auditLog)
    {
        // Pastikan milik company aktif
        if ($auditLog->company_id != session('company_id')) abort(403);

        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }
}