<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\Plan;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
            Auth::login($user);

            $invitationToken = $request->query('invitation');
            if ($invitationToken) {
                $invitation = \App\Models\Invitation::where('token', $invitationToken)->first();
                if ($invitation && $invitation->isValid() && $invitation->email === $user->email) {
                    
                    // Attach Company
                    $user->companies()->attach($invitation->company_id, ['role' => $invitation->role]);
                    
                    // TAMBAHKAN: Attach Cabang yang dipilih
                    if (!empty($invitation->branch_ids)) {
                        $user->branches()->attach($invitation->branch_ids);
                    }
                    
                    $invitation->update(['accepted_at' => now()]);
                    session(['company_id' => $invitation->company_id]);
                    
                    DB::commit();
                    return redirect()->route('dashboard')
                        ->with('success', 'Selamat! Anda bergabung ke ' . $invitation->company->name);
                }
            }
            
    $company = Company::create([
    'name'                    => 'Perusahaan ' . $user->name,
    'business_type'           => null,     // ← NULL, user pilih di onboarding
    'onboarding_completed_at' => null,     // ← NULL, wajib onboarding
    ]);
    $user->companies()->attach($company->id, ['role' => 'owner']);
    session(['company_id' => $company->id]);


    $proPlan = Plan::where('code', 'professional')->first() ?? Plan::first();
    if ($proPlan) {
    Subscription::create([
        'company_id'    => $company->id,
        'plan_id'       => $proPlan->id,
        'status'        => 'trial',           
        'billing_cycle' => 'monthly',
        'trial_ends_at' => now()->addDays(14),
        'starts_at'     => now(),
        'ends_at'       => now()->addDays(14),
    ]);
    }

DB::commit();

// 5. Redirect ke onboarding (bukan dashboard)
return redirect()->route('onboarding.index')
    ->with('success', 'Selamat datang! Pilih jenis bisnis Anda untuk memulai.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage()]);
        }
    }
}