<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex flex-row flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                {{ __('Generated Short Urls') }}
            </h3>
            <div class="flex flex-wrap items-center gap-4">
                <form method="GET" action="{{ $filterFormAction ?? route('short-urls.index') }}" class="flex items-center flex-wrap">
                    @foreach (request()->except('filter') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <select id="filter-select" name="filter" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        @foreach (\App\Http\Controllers\ShortUrlController::FILTER_OPTIONS as $value => $label)
                            <option value="{{ $value }}" {{ $filter === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('short-urls.download', ['filter' => $filter]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Download') }}</a>
                @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <a href="{{ route('short-urls.view-all', ['filter' => $filter]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 ml-8">
                        {{ __('View All') }}
                    </a>
                @endif
            </div>
        </div>

        @cannot('create', \App\Models\ShortUrl::class)
            <p class="text-gray-500 mb-4">{{ __('As SuperAdmin you can view all short URLs but cannot create them.') }}</p>
        @endcannot
        @if (auth()->user()->isMember())
            <p class="text-gray-500 mb-4">{{ __('You only see short URLs created by yourself.') }}</p>
        @endif

        @if ($shortUrls->isEmpty())
            <p class="text-gray-500">{{ __('No short URLs yet.') }}</p>
            @can('create', \App\Models\ShortUrl::class)
                <a href="{{ route('short-urls.create') }}" class="mt-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Generate') }}</a>
            @endcan
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Short URL') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Original URL') }}</th>
                        @if (auth()->user()->isSuperAdmin())
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Company') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Created by') }}</th>
                        @endif
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Clicks') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($shortUrls as $shortUrl)
                        <tr>
                            <td class="px-4 py-2">
                                <a href="{{ url('/s/' . $shortUrl->short_code) }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">
                                    {{ url('/s/' . $shortUrl->short_code) }}
                                </a>
                            </td>
                            <td class="px-4 py-2 max-w-xs truncate" title="{{ $shortUrl->long_url }}">{{ $shortUrl->long_url }}</td>
                            @if (auth()->user()->isSuperAdmin())
                                <td class="px-4 py-2">{{ $shortUrl->company->name }}</td>
                                <td class="px-4 py-2">{{ $shortUrl->user->name }}</td>
                            @endif
                            <td class="px-4 py-2">{{ $shortUrl->clicks }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (method_exists($shortUrls, 'links'))
                <div class="mt-4">
                    {{ $shortUrls->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

