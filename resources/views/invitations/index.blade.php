<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invitations') }}
            </h2>
            @can('create', \App\Models\Invitation::class)
                <a href="{{ route('invitations.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    {{ __('Invite user') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($invitations->isEmpty())
                        <p class="text-gray-500">{{ __('No invitations yet.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Company') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Expires') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($invitations as $inv)
                                    <tr>
                                        <td class="px-4 py-2">{{ $inv->email }}</td>
                                        <td class="px-4 py-2">{{ $inv->company->name }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($inv->role) }}</td>
                                        <td class="px-4 py-2">
                                            @if ($inv->isAccepted())
                                                <span class="text-green-600">{{ __('Accepted') }}</span>
                                            @elseif ($inv->isExpired())
                                                <span class="text-red-600">{{ __('Expired') }}</span>
                                            @else
                                                <span class="text-amber-600">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $inv->expires_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $invitations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
