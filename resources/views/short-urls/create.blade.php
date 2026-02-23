<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create short URL') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('short-urls.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="long_url" :value="__('Original URL')" />
                            <x-text-input id="long_url" class="block mt-1 w-full" type="url" name="long_url" :value="old('long_url')" placeholder="https://example.com/page" required autofocus />
                            <x-input-error :messages="$errors->get('long_url')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('short-urls.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
                            <x-primary-button class="ms-3">{{ __('Create short URL') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
