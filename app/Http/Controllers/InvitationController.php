<?php
namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        $invitations = Invitation::where('company_id', $companyId)
            ->with('inviter')
            ->latest()
            ->paginate(20);
        return view('invitations.index', compact('invitations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,staff',
        ]);

        $companyId = session('company_id');
        $role = auth()->user()->companies()->where('companies.id', $companyId)->first()?->pivot->role;

        // Hanya owner/admin yang bisa undang
        if (!in_array($role, ['owner', 'admin'])) {
            abort(403, 'Hanya owner atau admin yang bisa mengundang user.');
        }

        // Cek apakah sudah jadi member
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser && $existingUser->companies()->where('companies.id', $companyId)->exists()) {
            return back()->with('error', 'User dengan email ini sudah menjadi member.');
        }

        // Cek invitation yang masih aktif
        $existingInvitation = Invitation::where('company_id', $companyId)
            ->where('email', $data['email'])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'Undangan untuk email ini masih aktif. Tunggu atau batalkan dulu.');
        }

        DB::transaction(function () use ($data, $companyId) {
            $invitation = Invitation::create([
                'company_id' => $companyId,
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => Invitation::generateToken(),
                'invited_by' => auth()->id(),
                'expires_at' => now()->addDays(7),
            ]);

            // Kirim email (kalau MAIL sudah dikonfigurasi)
            try {
                Mail::to($data['email'])->send(new InvitationMail($invitation));
            } catch (\Exception $e) {
                \Log::warning('Invitation mail failed: ' . $e->getMessage());
            }
        });

        return back()->with('success', 'Undangan dikirim ke ' . $data['email']);
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect('/login')->with('error', 'Undangan tidak valid.');
        }

        if ($invitation->isExpired()) {
            return redirect('/login')->with('error', 'Undangan sudah kedaluwarsa.');
        }

        if ($invitation->isAccepted()) {
            return redirect('/login')->with('info', 'Undangan sudah pernah diterima. Silakan login.');
        }

        // Kalau user belum login → redirect ke register dengan email pre-filled
        if (!auth()->check()) {
            return redirect()->route('register', ['invitation' => $token]);
        }

        // Kalau user sudah login → langsung attach ke company
        $user = auth()->user();

        if ($user->email !== $invitation->email) {
            return redirect('/dashboard')->with('error', 'Email Anda tidak sesuai dengan undangan.');
        }

        if ($user->companies()->where('companies.id', $invitation->company_id)->exists()) {
            $invitation->update(['accepted_at' => now()]);
            return redirect('/dashboard')->with('info', 'Anda sudah menjadi member company ini.');
        }

        DB::transaction(function () use ($user, $invitation) {
            $user->companies()->attach($invitation->company_id, ['role' => $invitation->role]);
            $invitation->update(['accepted_at' => now()]);
            session(['company_id' => $invitation->company_id]);
        });

        return redirect('/dashboard')->with('success', 'Selamat! Anda bergabung ke ' . $invitation->company->name);
    }

    public function destroy(Invitation $invitation)
    {
        if ($invitation->company_id != session('company_id')) abort(403);
        $invitation->delete();
        return back()->with('success', 'Undangan dibatalkan.');
    }
}