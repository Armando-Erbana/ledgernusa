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
            // 1. Buat User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
            Auth::login($user);

            // 2. Cek apakah mendaftar lewat link undangan
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

            // 3. Jika daftar mandiri (tanpa undangan)
            $company = Company::create([
                'name' => 'Perusahaan ' . $user->name,
            ]);
            $user->companies()->attach($company->id, ['role' => 'owner']);
            session(['company_id' => $company->id]);

            // 4. Berikan Trial Pro 7 Hari
            $proPlan = Plan::where('code', 'pro')->first();
            $planId = $proPlan ? $proPlan->id : 2;

            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $planId, 
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => now()->addDays(7),
            ]);

            DB::commit();

            return redirect(route('dashboard', absolute: false))
                ->with('success', 'Selamat datang! Anda mendapatkan akses Trial Pro selama 7 hari.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage()]);
        }
    }
}