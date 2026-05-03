<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">
            {{ __('Edit manual invoice') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-xl space-y-6">
        <x-app-card>
            <form method="post" action="{{ route('manual-rechnungen.update', $rechnung) }}" class="space-y-6">
                @csrf
                @method('patch')
                @include('rechnungen.manual._form', ['rechnung' => $rechnung])
                <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-6">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('manual-rechnungen.index') }}" class="text-sm font-medium text-gray-600 underline-offset-2 hover:text-gray-950 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Cancel') }}</a>
                </div>
            </form>
        </x-app-card>

        @can('delete', $rechnung)
            <x-app-card class="border-red-100 ring-1 ring-red-100">
                <h3 class="text-base font-semibold text-gray-950">{{ __('Delete invoice') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('Only possible when open and not matched to bank payments.') }}</p>
                <form method="post" action="{{ route('manual-rechnungen.destroy', $rechnung) }}" class="mt-4" onsubmit="return confirm(@json(__('Delete this invoice?')));">
                    @csrf
                    @method('delete')
                    <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                </form>
            </x-app-card>
        @endcan
    </div>
</x-app-layout>
