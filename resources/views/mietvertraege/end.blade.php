<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('End lease') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl">
                <p class="text-sm text-gray-700 mb-4">
                    {{ __('Unit') }}: {{ $mietvertrag->einheit->name }} — {{ __('Tenant') }}: {{ $mietvertrag->mieter->name }}
                </p>
                <p class="text-sm text-amber-800 bg-amber-50 border border-amber-100 rounded p-3 mb-4">
                    {{ __('Open rent invoices are not cancelled automatically.') }}
                </p>
                <form method="post" action="{{ route('mietvertraege.end.store', $mietvertrag) }}">
                    @csrf
                    <div>
                        <x-input-label for="ended_on" :value="__('End date')" />
                        <x-text-input id="ended_on" name="ended_on" type="date" class="mt-1 block w-full" :value="old('ended_on', now()->format('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('ended_on')" />
                    </div>
                    <div class="mt-6 flex gap-4">
                        <x-danger-button type="submit">{{ __('Confirm end') }}</x-danger-button>
                        <a href="{{ route('mietvertraege.index') }}" class="text-sm text-gray-600">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
