<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">{{ __('New lease') }}</h2>
    </x-slot>
    <div class="mx-auto max-w-xl space-y-6">
        @if ($einheiten->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                {{ __('No free units — every unit already has an active lease, or create a unit first.') }}
                <a href="{{ route('einheiten.create') }}" class="ms-1 font-medium text-amber-950 underline underline-offset-2 hover:no-underline">{{ __('Add unit') }}</a>
            </div>
        @endif
        <x-app-card>
            <form method="post" action="{{ route('mietvertraege.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="einheit_id" :value="__('Unit')" />
                    <select id="einheit_id" name="einheit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled($einheiten->isEmpty())>
                        @foreach ($einheiten as $e)
                            <option value="{{ $e->id }}" @selected((int) old('einheit_id') === $e->id)>{{ $e->objekt->name }} — {{ $e->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('einheit_id')" />
                </div>
                <div>
                    <x-input-label for="mieter_id" :value="__('Tenant')" />
                    <select id="mieter_id" name="mieter_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach ($mieter as $m)
                            <option value="{{ $m->id }}" @selected((int) old('mieter_id') === $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('mieter_id')" />
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="starts_on" :value="__('Start date')" />
                        <x-text-input id="starts_on" name="starts_on" type="date" class="mt-1 block w-full" :value="old('starts_on')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('starts_on')" />
                    </div>
                    <div>
                        <x-input-label for="ends_on" :value="__('End date (optional)')" />
                        <x-text-input id="ends_on" name="ends_on" type="date" class="mt-1 block w-full" :value="old('ends_on')" />
                        <x-input-error class="mt-2" :messages="$errors->get('ends_on')" />
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="kaltmiete" :value="__('Cold rent (EUR / month)')" />
                        <x-text-input id="kaltmiete" name="kaltmiete" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('kaltmiete')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('kaltmiete')" />
                    </div>
                    <div>
                        <x-input-label for="nk_vorauszahlung" :value="__('NK advance (EUR / month)')" />
                        <x-text-input id="nk_vorauszahlung" name="nk_vorauszahlung" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('nk_vorauszahlung')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nk_vorauszahlung')" />
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-6">
                    @if ($einheiten->isNotEmpty() && $mieter->isNotEmpty())
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    @endif
                    <a href="{{ route('mietvertraege.index') }}" class="text-sm font-medium text-gray-600 underline-offset-2 hover:text-gray-950 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Cancel') }}</a>
                </div>
            </form>
        </x-app-card>
    </div>
</x-app-layout>
