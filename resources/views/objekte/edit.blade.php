<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit property') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl">
                <form method="post" action="{{ route('objekte.update', $objekt) }}">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $objekt->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div class="mt-6 flex gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('objekte.index') }}" class="text-sm text-gray-600">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-xl border border-red-100">
                <form method="post" action="{{ route('objekte.destroy', $objekt) }}" onsubmit="return confirm(@json(__('Delete this property?')));">
                    @csrf
                    @method('delete')
                    <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
