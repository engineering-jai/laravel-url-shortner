<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Company::class);

        $companies = Company::withCount(['users', 'shortUrls'])
            ->withSum('shortUrls as total_hits', 'clicks')
            ->orderBy('name')
            ->get();

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Company::create($validated);

        return redirect()->route('invitations.create')->with('status', __('Company created. You can now send an invitation.'));
    }
}
