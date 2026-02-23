<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Team members') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ __('Team members') }}
                        </h3>
                        <a href="{{ route('invitations.create') }}" class="inline-flex items-center rounded-md border border-indigo-600 bg-indigo-600 px-3 py-2 text-sm font-medium !text-white no-underline shadow-sm hover:bg-indigo-500 hover:!text-white">
                            {{ __('Invite') }}
                        </a>
                    </div>

                    @if ($teamMembers->isNotEmpty())
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
                    @else
                        <p class="text-gray-500">{{ __('No team members yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
