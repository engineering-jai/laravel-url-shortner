<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    /**
     * Display all team members (Admin's company) in one page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! $user->isAdmin() || ! $user->company_id) {
            abort(403);
        }

        $teamMembers = User::where('company_id', $user->company_id)
            ->withCount('shortUrls')
            ->withSum('shortUrls as total_hits', 'clicks')
            ->orderBy('name')
            ->get();

        return view('team-members.index', compact('teamMembers'));
    }
}
