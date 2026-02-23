<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (auth()->user()->isSuperAdmin())
                {{ __('Super Admin Dashboard') }}
            @elseif (auth()->user()->isAdmin())
                {{ __('Admin Dashboard') }}
            @else
                {{ __('Dashboard') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (auth()->user()->isAdmin())
                <!-- Admin: Short URLs card (same as /short-urls) -->
                @isset($shortUrls, $filter)
                    <div class="border-b border-gray-200 pb-8 mb-8">
                        @include('short-urls._list', [
                            'shortUrls' => $shortUrls,
                            'filter' => $filter,
                            'filterFormAction' => route('dashboard'),
                        ])
                    </div>
                @endisset

                <!-- Admin: Team members card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ __('Team members') }}
                            </h3>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('team-members.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                                    {{ __('View All') }}
                                </a>
                                <a href="{{ route('invitations.create') }}" class="inline-flex items-center rounded-md border border-indigo-600 bg-indigo-600 px-3 py-2 text-sm font-medium !text-white no-underline shadow-sm hover:bg-indigo-500 hover:!text-white">
                                    {{ __('Invite') }}
                                </a>
                            </div>
                        </div>

                        @if(isset($teamMembers) && $teamMembers->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('User / Email') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Generated Url') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total url hits') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($teamMembers as $member)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <span class="font-medium">{{ $member->name }}</span><br>
                                                <span class="text-sm text-gray-500">{{ $member->email }}</span>
                                            </td>
                                            <td class="px-4 py-2">{{ ucfirst($member->role) }}</td>
                                            <td class="px-4 py-2">{{ $member->short_urls_count }}</td>
                                            <td class="px-4 py-2">{{ $member->total_hits ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $teamMembers->links() }}
                            </div>
                        @else
                            <p class="text-gray-500">{{ __('No team members yet.') }}</p>
                        @endif
                    </div>
                </div>
            @elseif (! auth()->user()->isSuperAdmin())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            @else
                <!-- Client section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b border-gray-200 pb-8 mb-8">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ __('Client') }}
                            </h3>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('companies.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                                    {{ __('View All') }}
                                </a>
                                <a href="{{ route('invitations.create') }}" class="inline-flex items-center rounded-md border border-indigo-600 bg-indigo-600 px-3 py-2 text-sm font-medium !text-white no-underline shadow-sm hover:bg-indigo-500 hover:!text-white">
                                    {{ __('Invite') }}
                                </a>
                            </div>
                        </div>

                        @if(isset($companies) && $companies->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Client name') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Users') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Generated Urls') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total hits') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($companies as $company)
                                        <tr>
                                            <td class="px-4 py-2">{{ $company->name }}</td>
                                            <td class="px-4 py-2">{{ $company->users_count }}</td>
                                            <td class="px-4 py-2">{{ $company->short_urls_count }}</td>
                                            <td class="px-4 py-2">{{ $company->total_hits ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $companies->links() }}
                            </div>
                        @else
                            <p class="text-gray-500">{{ __('No clients yet.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Short URLs section (same as /short-urls) -->
                @isset($shortUrls, $filter)
                    <div>
                        @include('short-urls._list', [
                            'shortUrls' => $shortUrls,
                            'filter' => $filter,
                            'filterFormAction' => route('dashboard'),
                        ])
                    </div>
                @endisset
            @endif
        </div>
    </div>
</x-app-layout>
