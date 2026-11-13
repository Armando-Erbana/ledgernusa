<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        $company = auth()->user()->activeCompany();
        $users = $company->users()->with('positions')->orderBy('name')->get();
        $planCode = strtolower($company->subscription?->plan?->code ?? 'starter');
        $isPro = in_array($planCode, ['pro', 'enterprise']);
        $branches = $isPro ? Branch::where('company_id', $companyId)->get() : collect();
        $positions = $isPro ? Position::whereHas('division', function($q) use ($companyId) { $q->where('company_id', $companyId); })->get() : collect();
        return view('users.index', compact('users', 'isPro', 'branches', 'positions'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff',
            'branch_ids' => 'nullable|array',
            'position_ids' => 'nullable|array'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->companies()->where('companies.id', $companyId)->exists()) {
                return back()->withInput()->with('error', 'User dengan email ini sudah terdaftar di perusahaan Anda.');
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        DB::transaction(function () use ($user, $companyId, $request) {
            $user->companies()->attach($companyId, ['role' => $request->role]);
            if ($request->role === 'staff' && !empty($request->branch_ids)) {
                $user->branches()->attach($request->branch_ids);
            }
            if (!empty($request->position_ids)) {
                $user->positions()->sync($request->position_ids);
            }
        });

        return back()->with('success', 'User berhasil ditambahkan ke perusahaan.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        $user->companies()->detach(session('company_id'));
        if ($user->companies()->count() === 0) $user->delete();
        return back()->with('success', 'User berhasil dihapus dari perusahaan.');
    }
}