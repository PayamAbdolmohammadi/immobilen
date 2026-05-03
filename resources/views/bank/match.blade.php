<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">
                {{ __('Bank matching') }}
            </h2>
            <a href="{{ route('bank.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('CSV import') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('status'))
            <div class="rounded-lg border px-4 py-3 text-sm @if (session('status') === 'payment-allocated') border-green-300 bg-green-50 text-green-950 @else border-green-200 bg-green-50 text-green-900 @endif" role="status">
                @if (session('status') === 'bank-import-queued')
                    {{ __('Import queued or finished — you can match new lines below.') }}
                @elseif (session('status') === 'bank-imported')
                    {{ __('Import completed.') }}
                @elseif (session('status') === 'payment-allocated')
                    <span class="font-semibold">{{ __('Bank matching allocated success detail') }}</span>
                @endif
            </div>
        @endif

        <x-app-card>
            <h3 class="text-base font-semibold text-gray-950">{{ __('Bank matching') }}</h3>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ __('Bank matching hero hint') }}</p>
            <p class="mt-2 text-xs text-gray-500">{{ __('Allocate bank lines to open invoices (Zuordnen). Nothing is booked automatically — hints are suggestions only.') }}</p>
        </x-app-card>

        @include('bank.partials.transactions-table')
    </div>
</x-app-layout>
