<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit lease') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl">
                <p class="text-sm text-gray-600 mb-4">{{ __('Amount changes apply to future rent invoices only; issued invoices stay unchanged.') }}</p>
                <form method="post" action="{{ route('mietvertraege.update', $mietvertrag) }}">
                    @csrf
                    @method('patch')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="kaltmiete" :value="__('Cold rent (EUR / month)')" />
                            <x-text-input id="kaltmiete" name="kaltmiete" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('kaltmiete', number_format($kaltmiete_euro, 2, '.', ''))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('kaltmiete')" />
                        </div>
                        <div>
                            <x-input-label for="nk_vorauszahlung" :value="__('NK advance (EUR / month)')" />
                            <x-text-input id="nk_vorauszahlung" name="nk_vorauszahlung" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('nk_vorauszahlung', number_format($nk_euro, 2, '.', ''))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nk_vorauszahlung')" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="ends_on" :value="__('Planned end (optional)')" />
                        <x-text-input id="ends_on" name="ends_on" type="date" class="mt-1 block w-full" :value="old('ends_on', $mietvertrag->ends_on?->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('ends_on')" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="faelligkeit_tag" :value="__('Due day of month (1–31, optional)')" />
                        <x-text-input id="faelligkeit_tag" name="faelligkeit_tag" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('faelligkeit_tag', $mietvertrag->faelligkeit_tag)" />
                        <x-input-error class="mt-2" :messages="$errors->get('faelligkeit_tag')" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="zahlungsintervall" :value="__('Payment interval')" />
                        <select id="zahlungsintervall" name="zahlungsintervall" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="monthly" @selected(old('zahlungsintervall', $mietvertrag->zahlungsintervall) === 'monthly')>{{ __('monthly') }}</option>
                        </select>
                    </div>
                    <div class="mt-6 flex gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('mietvertraege.index') }}" class="text-sm text-gray-600">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
