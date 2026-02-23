<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ShortUrlController;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($request);
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        }

        return view('dashboard');
    }

    private function superAdminDashboard(Request $request): View
    {
        $companies = Company::withCount(['users', 'shortUrls'])
            ->withSum('shortUrls as total_hits', 'clicks')
            ->orderBy('name')
            ->paginate(2, ['*'], 'clients_page')
            ->withQueryString();

        $shortUrlQuery = ShortUrl::with(['company', 'user']);
        $filter = $this->resolveFilter($request);
        $this->applyDateFilter($shortUrlQuery, $filter);

        $shortUrls = $shortUrlQuery->latest()
            ->paginate(2, ['*'], 'short_urls_page')
            ->withQueryString();

        return view('dashboard', compact('companies', 'shortUrls', 'filter'));
    }

    private function adminDashboard(Request $request): View
    {
        $user = $request->user();

        $teamMembers = User::where('company_id', $user->company_id)
            ->withCount('shortUrls')
            ->withSum('shortUrls as total_hits', 'clicks')
            ->orderBy('name')
            ->paginate(2, ['*'], 'team_members_page')
            ->withQueryString();

        $shortUrlQuery = ShortUrl::with(['company', 'user'])
            ->where('company_id', $user->company_id);
        $filter = $this->resolveFilter($request);
        $this->applyDateFilter($shortUrlQuery, $filter);

        $shortUrls = $shortUrlQuery->latest()
            ->paginate(2, ['*'], 'short_urls_page')
            ->withQueryString();

        return view('dashboard', compact('teamMembers', 'shortUrls', 'filter'));
    }

    private function resolveFilter(Request $request): string
    {
        $filter = $request->input('filter', ShortUrlController::FILTER_THIS_MONTH);
        if (! array_key_exists($filter, ShortUrlController::FILTER_OPTIONS)) {
            $filter = ShortUrlController::FILTER_THIS_MONTH;
        }
        return $filter;
    }

    private function applyDateFilter($query, string $filter): void
    {
        $now = Carbon::now();

        match ($filter) {
            ShortUrlController::FILTER_TODAY => $query->whereBetween('created_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()]),
            ShortUrlController::FILTER_THIS_WEEK => $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now]),
            ShortUrlController::FILTER_LAST_WEEK => $query->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]),
            ShortUrlController::FILTER_THIS_MONTH => $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now]),
            ShortUrlController::FILTER_LAST_MONTH => $query->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()]),
            default => null,
        };
    }
}

