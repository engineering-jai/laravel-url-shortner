<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invitation::class);

        $user = $request->user();
        $query = Invitation::with(['company', 'inviter']);

        if ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $invitations = $query->latest()->paginate(15);

        return view('invitations.index', compact('invitations'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Invitation::class);

        $user = $request->user();
        $companies = $user->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : collect([$user->company]);

        $roles = $user->isSuperAdmin()
            ? [User::ROLE_ADMIN => 'Admin']
            : [User::ROLE_ADMIN => 'Admin', User::ROLE_MEMBER => 'Member'];

        return view('invitations.create', compact('companies', 'roles'));
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $companyId = $request->validated('company_id');
        $company = Company::findOrFail($companyId);

        $invitation = Invitation::create([
            'company_id' => $companyId,
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'token' => Str::random(64),
            'invited_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);

        \Illuminate\Support\Facades\Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return redirect()->route('invitations.index')
            ->with('status', __('Invitation sent to :email.', ['email' => $invitation->email]));
    }

    /**
     * Show accept-invitation form (guest).
     */
    public function acceptShow(Request $request, string $token): View|RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', __('This invitation has expired.'));
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', __('This invitation has already been used.'));
        }

        return view('invitations.accept', ['invitation' => $invitation]);
    }

    /**
     * Accept invitation: create or update user, log in, mark accepted.
     */
    public function acceptStore(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'exists:invitations,token'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $invitation = Invitation::where('token', $request->token)->firstOrFail();

        if ($invitation->isExpired() || $invitation->isAccepted()) {
            return redirect()->route('login')->with('error', __('This invitation is no longer valid.'));
        }

        $user = User::firstOrCreate(
            ['email' => $invitation->email],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]
        );

        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]);
        }

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', __('Welcome! You have joined :company.', ['company' => $invitation->company->name]));
    }
}
