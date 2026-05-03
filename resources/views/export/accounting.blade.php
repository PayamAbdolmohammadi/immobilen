<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">
            {{ __('Accounting export') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-app-card>
            <h3 class="text-base font-semibold text-gray-950">{{ __('Export for tax advisor title') }}</h3>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ __('Export for tax advisor intro') }}</p>
            <div class="mt-4">
                <x-app-badge variant="amber">{{ __('DATEV ready unofficial badge') }}</x-app-badge>
            </div>
            <p class="mt-3 text-xs text-gray-500">{{ __('German CSV format: UTF-8 with BOM, semicolon-separated.') }}</p>
        </x-app-card>

        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
            {{ __('This export is for bookkeeping preparation only and is not an official DATEV EXTF export. Please verify the data before sharing it with your tax advisor.') }}
        </div>

        <x-app-card class="space-y-8">
            <div>
                <h3 class="text-base font-semibold text-gray-950">{{ __('Simple CSV') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('German CSV format: UTF-8 with BOM, semicolon-separated.') }}</p>
                <form method="post" action="{{ route('export.accounting.simple-csv') }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="simple_year" :value="__('Year')" />
                            <x-text-input id="simple_year" name="year" type="number" min="2020" max="2100" class="mt-1 block w-full" :value="old('year', $defaultYear)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('year')" />
                        </div>
                        <div>
                            <x-input-label for="simple_month" :value="__('Month (optional)')" />
                            <select id="simple_month" name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All months') }}</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected((string) old('month') === (string) $m)>{{ $m }}</option>
                                @endfor
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('month')" />
                        </div>
                    </div>
                    <x-primary-button type="submit">{{ __('Export simple CSV') }}</x-primary-button>
                </form>
            </div>

            <div class="border-t border-gray-100 pt-8">
                <h3 class="text-base font-semibold text-gray-950">{{ __('DATEV-ready CSV') }}</h3>
                <form method="post" action="{{ route('export.accounting.datev-csv') }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="datev_year" :value="__('Year')" />
                            <x-text-input id="datev_year" name="year" type="number" min="2020" max="2100" class="mt-1 block w-full" :value="old('year', $defaultYear)" required />
                        </div>
                        <div>
                            <x-input-label for="datev_month" :value="__('Month (optional)')" />
                            <select id="datev_month" name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All months') }}</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected((string) old('month') === (string) $m)>{{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <fieldset>
                        <legend class="mb-2 block text-sm font-medium text-gray-700">{{ __('Chart of accounts') }}</legend>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-800">
                                <input type="radio" name="chart_of_accounts" value="SKR03" class="border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('chart_of_accounts', 'SKR03') === 'SKR03') />
                                SKR03
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-800">
                                <input type="radio" name="chart_of_accounts" value="SKR04" class="border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('chart_of_accounts') === 'SKR04') />
                                SKR04
                            </label>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('chart_of_accounts')" />
                    </fieldset>
                    <x-primary-button type="submit">{{ __('Export DATEV-ready CSV') }}</x-primary-button>
                </form>
            </div>
        </x-app-card>
    </div>
</x-app-layout>
