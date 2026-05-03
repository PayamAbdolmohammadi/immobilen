<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $today = now()->toDateString();
    @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        @if (session('status') === 'mahnung-sent')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                {{ __('Reminder email sent.') }}
            </div>
        @endif
        @if (session('status') === 'rechnung-storniert')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                {{ __('Rent invoice cancelled (Storno). You can regenerate this period via rents:generate-monthly if needed.') }}
            </div>
        @endif

        {{-- Hero (title comes from top bar) --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="max-w-xl text-sm text-gray-600">{{ __('Cashflow overview for your portfolio.') }}</p>
            </div>
            @if (auth()->user()?->isOwner())
                <div class="shrink-0">
                    <a
                        href="{{ route('demo-flow.index') }}"
                        class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ __('Dashboard CTA wow demo') }}
                    </a>
                </div>
            @endif
        </div>

        @if (auth()->user()?->isOwner())
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <p class="font-medium text-gray-950">{{ __('WOW demo sales guide title') }}</p>
                <p class="mt-1">{{ __('WOW demo sales guide hint') }}</p>
            </div>
        @endif

        {{-- KPIs --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-app-card class="!p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Open invoices') }}</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-gray-950">{{ $openCount }}</p>
            </x-app-card>
            <x-app-card class="!p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Overdue') }}</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-red-700">{{ $overdueCount }}</p>
            </x-app-card>
            <x-app-card class="!p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Dashboard KPI recent allocations') }}</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-gray-950">{{ $recentPayments->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('Dashboard KPI recent allocations hint') }}</p>
            </x-app-card>
            <a href="{{ route('bank.matching') }}" class="group block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Dashboard KPI matching CTA title') }}</p>
                <p class="mt-3 text-sm font-semibold text-indigo-600 group-hover:text-indigo-700">{{ __('Dashboard KPI matching CTA subtitle') }}</p>
                <p class="mt-4 text-xs font-medium text-gray-500">{{ __('Go to matching') }} →</p>
            </a>
        </div>

        {{-- Offene Posten --}}
        <section aria-labelledby="open-items-heading">
            <x-app-table>
                <x-slot:title>
                    <span id="open-items-heading">{{ __('Open items section') }}</span>
                </x-slot:title>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Invoice') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tenant') }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Open') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Due') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($openInvoicesPreview as $inv)
                            @php
                                $isOverdue = $inv->faellig_am && $inv->faellig_am->toDateString() < $today;
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-950">
                                    #{{ $inv->id }}
                                    @if ($inv->typ === \App\Models\Rechnung::TYP_RENT)
                                        <x-app-badge variant="gray" class="ms-1">{{ __('rent') }}</x-app-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $inv->mieter->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($inv->openAmountCent() / 100, 2, ',', '.') }} €</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $inv->faellig_am?->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($isOverdue)
                                        <x-app-badge variant="red">{{ __('Invoice status overdue badge') }}</x-app-badge>
                                    @else
                                        <x-app-badge variant="amber">{{ __('Invoice status open badge') }}</x-app-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col gap-2">
                                        @if ($isOverdue)
                                            @can('sendMahnung', $inv)
                                                <form method="post" action="{{ route('rechnungen.mahnung', $inv) }}" class="inline">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="!py-1.5 !px-3 !text-xs">{{ __('Send reminder') }}</x-secondary-button>
                                                </form>
                                            @elseif(! filled($inv->mieter?->email))
                                                <span class="text-xs text-gray-400" title="{{ __('Tenant needs an email address') }}">—</span>
                                            @endif
                                        @endif
                                        @can('storno', $inv)
                                            <form method="post" action="{{ route('rechnungen.storno', $inv) }}" class="space-y-1" onsubmit="return confirm(@json(__('Cancel this rent invoice? No bank allocations must exist.')));">
                                                @csrf
                                                <label class="flex items-center gap-2 text-xs text-gray-700">
                                                    <input type="checkbox" name="confirm" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" required />
                                                    {{ __('Confirm') }}
                                                </label>
                                                <x-secondary-button type="submit" class="!py-1.5 !px-3 !text-xs">{{ __('Storno') }}</x-secondary-button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10">
                                    <x-app-empty-state :title="__('No open invoices.')" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-app-table>
        </section>

        {{-- Letzte Aktivitäten --}}
        <section aria-labelledby="activity-heading">
            <x-app-table>
                <x-slot:title>
                    <span id="activity-heading">{{ __('Recent activity section') }}</span>
                </x-slot:title>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('When') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Invoice') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tenant') }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recentPayments as $z)
                            <tr class="transition-colors hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $z->created_at->format('Y-m-d H:i') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-950">#{{ $z->rechnung_id }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $z->rechnung->mieter->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($z->betrag_cent / 100, 2, ',', '.') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10">
                                    <x-app-empty-state :title="__('No allocations yet.')">
                                        {{ __('Dashboard activity empty hint') }}
                                    </x-app-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-app-table>
        </section>

        {{-- Mietvorschau --}}
        <section aria-labelledby="leases-heading">
            <x-app-card class="overflow-hidden !p-0">
                <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 id="leases-heading" class="text-base font-semibold text-gray-950">{{ __('Active leases — rent preview') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Amount from contract; actual invoices are separate snapshots when generated.') }}</p>
                    </div>
                    <a href="{{ route('mietvertraege.index') }}" class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Manage leases') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Unit') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tenant') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Next period hint') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Monthly rent (preview)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($leasePreviews as $row)
                                <tr class="transition-colors hover:bg-gray-50/80">
                                    <td class="px-4 py-3 text-gray-950">{{ $row['lease']->einheit->name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $row['lease']->mieter->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $row['next_period_hint'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($row['total_cent'] / 100, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10">
                                        <x-app-empty-state :title="__('No active leases.')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>
        </section>
    </div>
</x-app-layout>
