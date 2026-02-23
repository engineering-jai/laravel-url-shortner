<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Short URLs') }}
            </h2>
            @can('create', \App\Models\ShortUrl::class)
                <a href="{{ route('short-urls.create') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Generate') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            @include('short-urls._list', ['shortUrls' => $shortUrls, 'filter' => $filter])
        </div>
    </div>
</x-app-layout>
