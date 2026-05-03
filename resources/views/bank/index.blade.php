<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">
                {{ __('Bank import & matching') }}
            </h2>
            <a href="{{ route('bank.matching') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Go to matching') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('status'))
            <div class="rounded-lg border px-4 py-3 text-sm @if (session('status') === 'payment-allocated') border-green-300 bg-green-50 text-green-950 @else border-green-200 bg-green-50 text-green-900 @endif" role="status">
                @if (session('status') === 'bank-import-queued')
                    {{ __('Import queued or finished (see recent imports below).') }}
                @elseif (session('status') === 'bank-imported')
                    {{ __('Import completed.') }}
                @elseif (session('status') === 'payment-allocated')
                    <span class="font-semibold">{{ __('Bank matching allocated success detail') }}</span>
                @endif
            </div>
        @endif

        <x-app-card>
            <h3 class="text-base font-semibold text-gray-950">{{ __('Upload CSV') }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('First row: headers including booking date and amount (e.g. Buchungstag, Betrag). German decimals supported.') }}</p>
            <form method="post" action="{{ route('bank.import') }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div>
                    <x-input-label for="file" :value="__('File')" />
                    <input id="file" name="file" type="file" accept=".csv,.txt,text/csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700" required />
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>
                <div>
                    <x-input-label for="csv_profile" :value="__('CSV bank profile')" />
                    <select id="csv_profile" name="csv_profile" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="{{ \App\Domain\Bank\BankCsvImporter::PROFILE_GENERIC }}">{{ __('Generic / auto columns') }}</option>
                        <option value="{{ \App\Domain\Bank\BankCsvImporter::PROFILE_SPARKASSE }}">{{ __('Sparkasse-style') }}</option>
                        <option value="{{ \App\Domain\Bank\BankCsvImporter::PROFILE_COMDIRECT }}">{{ __('Comdirect-style') }}</option>
                    </select>
                </div>
                <x-primary-button type="submit">{{ __('Import') }}</x-primary-button>
            </form>
        </x-app-card>

        <x-app-table>
            <x-slot:title>{{ __('Recent imports') }}</x-slot:title>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('File') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Profile') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Rows') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('When') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Error') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($imports as $imp)
                        <tr class="transition-colors hover:bg-gray-50/80">
                            <td class="px-4 py-3 text-gray-950">{{ $imp->original_filename }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $imp->csv_profile ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $imp->status }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-700">{{ $imp->row_count }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $imp->created_at->format('Y-m-d H:i') }}</td>
                            <td class="max-w-xs break-words px-4 py-3 text-xs text-red-700">{{ $imp->error_message ? \Illuminate\Support\Str::limit($imp->error_message, 120) : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-app-empty-state :title="__('No imports yet.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-100 px-4 py-3">{{ $imports->links() }}</div>
        </x-app-table>

        @include('bank.partials.transactions-table')
    </div>
</x-app-layout>
