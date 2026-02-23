<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShortUrlController extends Controller
{
    public const FILTER_TODAY = 'today';
    public const FILTER_THIS_WEEK = 'this_week';
    public const FILTER_LAST_WEEK = 'last_week';
    public const FILTER_THIS_MONTH = 'this_month';
    public const FILTER_LAST_MONTH = 'last_month';

    public const FILTER_OPTIONS = [
        self::FILTER_THIS_MONTH => 'This Month',
        self::FILTER_LAST_MONTH => 'Last Month',
        self::FILTER_THIS_WEEK => 'This Week',
        self::FILTER_LAST_WEEK => 'Last Week',
        self::FILTER_TODAY => 'Today',
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ShortUrl::class);

        $user = $request->user();
        $query = ShortUrl::with(['company', 'user']);

        if ($user->isSuperAdmin()) {
        } elseif ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
        } else {
            $query->where('user_id', $user->id);
        }

        $filter = $request->input('filter', self::FILTER_THIS_MONTH);
        if (!array_key_exists($filter, self::FILTER_OPTIONS)) {
            $filter = self::FILTER_THIS_MONTH;
        }
        $this->applyDateFilter($query, $filter);

        $shortUrls = $query->latest()->paginate(2)->withQueryString();

        return view('short-urls.index', compact('shortUrls', 'filter'));
    }

    public function download(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', ShortUrl::class);

        $user = $request->user();
        $query = ShortUrl::with(['company', 'user']);

        if ($user->isSuperAdmin()) {
        } elseif ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
        } else {
            $query->where('user_id', $user->id);
        }

        $filter = $request->input('filter', self::FILTER_THIS_MONTH);
        if (!array_key_exists($filter, self::FILTER_OPTIONS)) {
            $filter = self::FILTER_THIS_MONTH;
        }
        $this->applyDateFilter($query, $filter);

        $shortUrls = $query->latest()->get();

        $filename = 'short-urls-' . $filter . '-' . Carbon::now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($shortUrls, $user) {
            $handle = fopen('php://output', 'w');
            if ($user->isSuperAdmin()) {
                fputcsv($handle, ['Short URL', 'Original URL', 'Company', 'Created By', 'Clicks', 'Created At']);
                foreach ($shortUrls as $row) {
                    fputcsv($handle, [
                        url('/s/' . $row->short_code),
                        $row->long_url,
                        $row->company->name ?? '',
                        $row->user->name ?? '',
                        $row->clicks,
                        $row->created_at->toDateTimeString(),
                    ]);
                }
            } else {
                fputcsv($handle, ['Short URL', 'Original URL', 'Clicks', 'Created At']);
                foreach ($shortUrls as $row) {
                    fputcsv($handle, [
                        url('/s/' . $row->short_code),
                        $row->long_url,
                        $row->clicks,
                        $row->created_at->toDateTimeString(),
                    ]);
                }
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function applyDateFilter($query, string $filter): void
    {
        $now = Carbon::now();

        match ($filter) {
            self::FILTER_TODAY => $query->whereBetween('created_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()]),
            self::FILTER_THIS_WEEK => $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now]),
            self::FILTER_LAST_WEEK => $query->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]),
            self::FILTER_THIS_MONTH => $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now]),
            self::FILTER_LAST_MONTH => $query->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()]),
            default => null,
        };
    }

    public function viewAll(Request $request): View
    {
        $this->authorize('viewAny', ShortUrl::class);

        $user = $request->user();
        $query = ShortUrl::with(['company', 'user']);

        if ($user->isSuperAdmin()) {
        } elseif ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
        } else {
            $query->where('user_id', $user->id);
        }

        $filter = $request->input('filter', self::FILTER_THIS_MONTH);
        if (!array_key_exists($filter, self::FILTER_OPTIONS)) {
            $filter = self::FILTER_THIS_MONTH;
        }
        $this->applyDateFilter($query, $filter);

        $shortUrls = $query->latest()->get();

        return view('short-urls.view-all', compact('shortUrls', 'filter'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', ShortUrl::class);

        return view('short-urls.create');
    }

    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $user = $request->user();

        $shortCode = $this->generateUniqueShortCode();

        ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'long_url' => $request->validated('long_url'),
            'short_code' => $shortCode,
        ]);

        return redirect()->route('short-urls.index')
            ->with('status', __('Short URL created.') . ' ' . url('/s/' . $shortCode));
    }

    private function generateUniqueShortCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }
}
