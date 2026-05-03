<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New NK settlement') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($objekte->isEmpty())
                <div class="p-4 bg-amber-50 text-amber-900 rounded-md text-sm mb-6">
                    {{ __('Create a property first.') }}
                    <a href="{{ route('objekte.create') }}" class="underline font-medium">{{ __('Add property') }}</a>
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-3xl">
                <p class="text-sm text-gray-600 mb-4">{{ __('Costs are split equally across all units on the property (MVP).') }}</p>
                <form method="post" action="{{ route('nk-abrechnungen.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="objekt_id" :value="__('Property')" />
                            <select id="objekt_id" name="objekt_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required @disabled($objekte->isEmpty())>
                                @foreach ($objekte as $o)
                                    <option value="{{ $o->id }}" @selected((int) old('objekt_id') === $o->id)>{{ $o->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('objekt_id')" />
                        </div>
                        <div>
                            <x-input-label for="jahr" :value="__('Year')" />
                            <x-text-input id="jahr" name="jahr" type="number" min="2000" max="2100" class="mt-1 block w-full" :value="old('jahr', (int) date('Y') - 1)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jahr')" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>{{ __('draft') }}</option>
                            <option value="final" @selected(old('status') === 'final')>{{ __('final') }}</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <span class="block font-medium text-sm text-gray-700 mb-2">{{ __('Cost lines (EUR)') }}</span>
                        @for ($i = 0; $i < 8; $i++)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                                <x-text-input type="text" name="positionen[{{ $i }}][beschreibung]" placeholder="{{ __('Description') }}" class="text-sm" :value="old('positionen.'.$i.'.beschreibung')" />
                                <x-text-input type="number" name="positionen[{{ $i }}][betrag]" step="0.01" min="0" placeholder="{{ __('Amount') }}" class="text-sm" :value="old('positionen.'.$i.'.betrag')" />
                            </div>
                        @endfor
                        <x-input-error class="mt-2" :messages="$errors->get('positionen')" />
                    </div>
                    <div class="mt-6 flex gap-4">
                        @if ($objekte->isNotEmpty())
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        @endif
                        <a href="{{ route('nk-abrechnungen.index') }}" class="text-sm text-gray-600">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
