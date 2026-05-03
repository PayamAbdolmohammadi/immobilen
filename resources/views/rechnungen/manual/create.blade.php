<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">
            {{ __('New manual invoice') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-xl">
        <x-app-card>
            <form method="post" action="{{ route('manual-rechnungen.store') }}" class="space-y-6">
                @csrf
                @include('rechnungen.manual._form', ['rechnung' => null])
                <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-6">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('manual-rechnungen.index') }}" class="text-sm font-medium text-gray-600 underline-offset-2 hover:text-gray-950 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Cancel') }}</a>
                </div>
            </form>
        </x-app-card>
    </div>
</x-app-layout>
