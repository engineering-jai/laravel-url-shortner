<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invite user') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($companies->isEmpty())
                        <p class="text-gray-600">{{ __('No companies yet.') }} {{ __('Create a company first.') }}</p>
                        @if (auth()->user()->isSuperAdmin())
                            <a href="{{ route('companies.create') }}" class="mt-2 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">{{ __('Create company') }}</a>
                        @endif
                    @else
                        <form method="POST" action="{{ route('invitations.store') }}">
                            @csrf

                            @if (auth()->user()->isSuperAdmin())
                                <div class="mt-4">
                                    <x-input-label for="company_id" :value="__('Company')" />
                                    <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                                </div>
                            @else
                                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                                <p class="mt-2 text-sm text-gray-600">{{ __('Inviting to your company:') }} <strong>{{ auth()->user()->company?->name }}</strong></p>
                                <p class="mt-1 text-sm text-gray-500">{{ __('You can invite another Admin or a Member to your company.') }}</p>
                            @endif

                            <div class="mt-4">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <a href="{{ route('invitations.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
                                <x-primary-button class="ms-3">{{ __('Send invitation') }}</x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
