<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Bitte vergeben Sie einen Namen für Ihre Hausverwaltung / Ihr Konto.') }}
    </div>

    <form method="POST" action="{{ route('mandant.setup.store') }}">
        @csrf

        <div>
            <x-input-label for="mandanten_name" :value="__('Mandant / Firma')" />
            <x-text-input id="mandanten_name" class="block mt-1 w-full" type="text" name="mandanten_name"
                :value="old('mandanten_name')" required autofocus maxlength="255" />
            <x-input-error :messages="$errors->get('mandanten_name')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Weiter zum Dashboard') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-6 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">{{ __('Logout') }}</button>
        </form>
    </div>
</x-guest-layout>
