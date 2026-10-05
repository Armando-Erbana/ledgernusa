<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));
    Auth::login($user);

    // Cek apakah ada invitation di query string
    $invitationToken = $request->query('invitation');
    if ($invitationToken) {
        $invitation = \App\Models\Invitation::where('token', $invitationToken)->first();
        if ($invitation && $invitation->isValid() && $invitation->email === $user->email) {
            $user->companies()->attach($invitation->company_id, ['role' => $invitation->role]);
            $invitation->update(['accepted_at' => now()]);
            session(['company_id' => $invitation->company_id]);
            return redirect()->route('dashboard')
                ->with('success', 'Selamat! Anda bergabung ke ' . $invitation->company->name);
        }
    }

    return redirect(route('dashboard', absolute: false));
}
}
