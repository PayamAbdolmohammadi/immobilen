<x-app-table>
    <x-slot:title>{{ __('Transactions') }}</x-slot:title>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50/80">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Date') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Amount') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Counterparty') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Reference') }}</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Remaining') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Suggested invoices section') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Allocate') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($transactions as $t)
                <tr class="align-top transition-colors hover:bg-gray-50/80">
                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $t->buchungsdatum->format('Y-m-d') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($t->betrag_cent / 100, 2, ',', '.') }} €</td>
                    <td class="px-4 py-3 text-gray-700">{{ $t->gegenpartei ?? '—' }}</td>
                    <td class="max-w-xs truncate px-4 py-3 text-gray-600" title="{{ $t->verwendungszweck }}">{{ $t->verwendungszweck ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($t->remainingPayableCent() / 100, 2, ',', '.') }} €</td>
                    <td class="px-4 py-3">
                        @php
                            $st = strtolower((string) $t->status);
                        @endphp
                        @if ($st === 'offen' || str_contains($st, 'open'))
                            <x-app-badge variant="amber">{{ $t->status }}</x-app-badge>
                        @else
                            <x-app-badge variant="gray">{{ $t->status }}</x-app-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 align-top text-xs text-gray-700">
                        @php
                            $hints = $matchSuggestions[$t->id] ?? [];
                        @endphp
                        @forelse ($hints as $hint)
                            <div class="mb-2 rounded-md border border-gray-100 bg-gray-50/80 px-2 py-1.5">
                                <span class="font-medium text-gray-950">#{{ $hint['rechnung']->id }}</span>
                                <span class="text-gray-700">{{ $hint['rechnung']->mieter->name }}</span>
                                @foreach ($hint['reasons'] as $reason)
                                    <span class="text-gray-500">
                                        @switch($reason)
                                            @case('exact_open_amount')
                                                [{{ __('Open amount matches remainder') }}]
                                                @break
                                            @case('tenant_in_text')
                                                [{{ __('Tenant name in bank text') }}]
                                                @break
                                            @case('invoice_id_in_text')
                                                [{{ __('Invoice number in bank text') }}]
                                                @break
                                            @default
                                                [{{ $reason }}]
                                        @endswitch
                                    </span>
                                @endforeach
                            </div>
                        @empty
                            <span class="text-gray-400">—</span>
                        @endforelse
                    </td>
                    <td class="px-4 py-3 align-top">
                        @if ($t->remainingPayableCent() > 0 && $openInvoices->isNotEmpty())
                            <form method="post" action="{{ route('bank.allocate', $t) }}" class="flex min-w-[14rem] flex-col gap-2">
                                @csrf
                                @if (request()->boolean('demo'))
                                    <input type="hidden" name="demo" value="1" />
                                @endif
                                @if (! empty($fromDemo))
                                    <input type="hidden" name="return_to_demo" value="1" />
                                @endif
                                <label class="sr-only" for="rechnung-{{ $t->id }}">{{ __('Invoice') }}</label>
                                <select id="rechnung-{{ $t->id }}" name="rechnung_id" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach ($openInvoices as $inv)
                                        @if ($inv->openAmountCent() > 0)
                                            <option value="{{ $inv->id }}">
                                                #{{ $inv->id }} {{ $inv->mieter->name }} — {{ number_format($inv->openAmountCent() / 100, 2, ',', '.') }} € {{ __('open') }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <x-text-input name="betrag" type="number" step="0.01" min="0.01" class="text-sm" placeholder="{{ __('Amount EUR') }}" required />
                                <x-input-error :messages="$errors->get('betrag')" />
                                <x-primary-button type="submit" class="!w-full sm:!w-auto !px-3 !py-2 !text-xs">{{ __('Allocate payment button') }}</x-primary-button>
                            </form>
                        @elseif ($t->remainingPayableCent() > 0)
                            <span class="text-xs text-gray-500">{{ __('No open invoices') }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-10">
                        <x-app-empty-state :title="__('No transactions.')" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-gray-100 px-4 py-3">{{ $transactions->links() }}</div>
</x-app-table>
