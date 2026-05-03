<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit unit') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl">
                <form method="post" action="{{ route('einheiten.update', $einheit) }}">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="objekt_id" :value="__('Property')" />
                        <select id="objekt_id" name="objekt_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach ($objekte as $o)
                                <option value="{{ $o->id }}" @selected((int) old('objekt_id', $einheit->objekt_id) === $o->id)>{{ $o->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('objekt_id')" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="name" :value="__('Unit name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $einheit->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div class="mt-6 flex gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('einheiten.index') }}" class="text-sm text-gray-600">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl border border-red-100">
                <form method="post" action="{{ route('einheiten.destroy', $einheit) }}" onsubmit="return confirm(@json(__('Delete this unit?')));">
                    @csrf
                    @method('delete')
                    <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
