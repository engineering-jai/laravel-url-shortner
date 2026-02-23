<x-guest-layout>
    @if (session('error'))
        <div class="mb-4 text-sm text-red-600">{{ session('error') }}</div>
    @endif
    <div class="mb-4 text-sm text-gray-600">
        {{ __('You have been invited to join :company as :role.', ['company' => $invitation->company->name, 'role' => ucfirst($invitation->role)]) }}
        {{ __('Set your name and password to accept.') }}
    </div>

    <form method="POST" action="{{ route('invitations.accept.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $invitation->token }}">

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $invitation->email)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>{{ __('Accept invitation') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
