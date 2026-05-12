<x-app-layout>
    <x-slot name="header">
        <div class="flex min-w-0 items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">{{ auth()->user()?->mandant?->name ?? __('Dashboard') }}</p>
                <h2 class="font-semibold leading-tight text-slate-950">
                    {{ __('Dashboard') }}
                </h2>
            </div>
            <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 sm:inline-flex">
                {{ now()->format('M Y') }}
            </span>
        </div>
    </x-slot>

    @php
        $today = now()->toDateString();
    @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        @if (session('status') === 'mahnung-sent')
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm" role="status">
                {{ __('Reminder email sent.') }}
            </div>
        @endif
        @if (session('status') === 'rechnung-storniert')
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm" role="status">
                {{ __('Rent invoice cancelled (Storno). You can regenerate this period via rents:generate-monthly if needed.') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-900 text-white shadow-2xl shadow-slate-200/70">
            <div class="grid gap-6 px-6 py-8 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:px-10 lg:py-10">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-200">Portfolio overview</p>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                        {{ __('Cashflow overview for your portfolio.') }}
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                        Behalten Sie offene Posten, Zahlungen, aktive Mietverträge und vorbereitete Exportschritte in einer Oberfläche im Blick.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-300 ring-1 ring-emerald-400/20">
                            {{ $openCount }} offene Vorgänge
                        </span>
                        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-white/10">
                            {{ $leasePreviews->count() }} aktive Mietverträge
                        </span>
                        <span class="inline-flex items-center rounded-full bg-violet-400/10 px-4 py-2 text-sm font-semibold text-violet-200 ring-1 ring-violet-400/20">
                            {{ $recentPayments->count() }} letzte Zuordnungen
                        </span>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="{{ route('bank.matching') }}"
                            class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition duration-200 hover:-translate-y-0.5 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            {{ __('Go to matching') }}
                        </a>
                        <a
                            href="{{ route('mietvertraege.index') }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            {{ __('Manage leases') }}
                        </a>
                        @if (auth()->user()?->isOwner())
                            <a
                                href="{{ route('demo-flow.index') }}"
                                class="inline-flex items-center justify-center rounded-full border border-indigo-300/20 bg-indigo-400/10 px-5 py-3 text-sm font-semibold text-indigo-100 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-400/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            >
                                {{ __('Dashboard CTA wow demo') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-sm text-slate-300">{{ __('Open invoices') }}</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $openCount }}</p>
                        <p class="mt-2 text-sm text-slate-300">aktuell offen und für Nachverfolgung sichtbar</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-sm text-slate-300">{{ __('Overdue') }}</p>
                        <p class="mt-3 text-3xl font-semibold {{ $overdueCount > 0 ? 'text-rose-300' : 'text-white' }}">{{ $overdueCount }}</p>
                        <p class="mt-2 text-sm text-slate-300">überfällige Vorgänge mit Handlungsbedarf</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-sm text-slate-300">{{ __('Dashboard KPI recent allocations') }}</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $recentPayments->count() }}</p>
                        <p class="mt-2 text-sm text-slate-300">{{ __('Dashboard KPI recent allocations hint') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-sm text-slate-300">Aktive Mietverträge</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $leasePreviews->count() }}</p>
                        <p class="mt-2 text-sm text-slate-300">mit Vorschau auf die nächste Abrechnungsperiode</p>
                    </div>
                </div>
            </div>
        </section>

        @if (auth()->user()?->isOwner())
            <div class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-sm font-semibold text-slate-950">{{ __('WOW demo sales guide title') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ __('WOW demo sales guide hint') }}</p>
            </div>
        @endif

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <x-app-card class="rounded-[1.75rem] border-slate-200 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">{{ __('Open items section') }}</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Offene Posten und nächste Schritte</h2>
                        <p class="mt-2 text-sm text-slate-600">Überfällige und offene Rechnungen mit direkten Aktionen für Erinnerung und Storno.</p>
                    </div>
                    <a
                        href="{{ route('manual-rechnungen.index') }}"
                        class="shrink-0 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                    >
                        {{ __('Invoices') }}
                    </a>
                </div>

                <div class="mt-6 overflow-x-auto rounded-[1.25rem] border border-slate-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Invoice') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Tenant') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Open') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Due') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($openInvoicesPreview as $inv)
                                @php
                                    $isOverdue = $inv->faellig_am && $inv->faellig_am->toDateString() < $today;
                                @endphp
                                <tr class="transition-colors hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-950">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">#{{ $inv->id }}</span>
                                            @if ($inv->typ === \App\Models\Rechnung::TYP_RENT)
                                                <x-app-badge variant="gray">{{ __('rent') }}</x-app-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-700">{{ $inv->mieter->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums font-medium text-slate-950">{{ number_format($inv->openAmountCent() / 100, 2, ',', '.') }} €</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $inv->faellig_am?->format('Y-m-d') ?? '—' }}</td>
                                    <td class="px-4 py-4">
                                        @if ($isOverdue)
                                            <x-app-badge variant="red">{{ __('Invoice status overdue badge') }}</x-app-badge>
                                        @else
                                            <x-app-badge variant="amber">{{ __('Invoice status open badge') }}</x-app-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex flex-wrap gap-2">
                                            @if ($isOverdue)
                                                @can('sendMahnung', $inv)
                                                    <form method="post" action="{{ route('rechnungen.mahnung', $inv) }}" class="inline">
                                                        @csrf
                                                        <x-secondary-button type="submit" class="!rounded-full !border-slate-300 !px-3.5 !py-2 !text-xs !font-semibold">
                                                            {{ __('Send reminder') }}
                                                        </x-secondary-button>
                                                    </form>
                                                @elseif(! filled($inv->mieter?->email))
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-400" title="{{ __('Tenant needs an email address') }}">—</span>
                                                @endif
                                            @endif
                                            @can('storno', $inv)
                                                <form method="post" action="{{ route('rechnungen.storno', $inv) }}" class="flex flex-wrap items-center gap-2" onsubmit="return confirm(@json(__('Cancel this rent invoice? No bank allocations must exist.')));">
                                                    @csrf
                                                    <label class="flex items-center gap-2 rounded-full bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                                        <input type="checkbox" name="confirm" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" required />
                                                        {{ __('Confirm') }}
                                                    </label>
                                                    <x-secondary-button type="submit" class="!rounded-full !border-slate-300 !px-3.5 !py-2 !text-xs !font-semibold">
                                                        {{ __('Storno') }}
                                                    </x-secondary-button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10">
                                        <x-app-empty-state :title="__('No open invoices.')" class="rounded-[1.25rem] border-dashed border-slate-200 bg-slate-50" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>

            <div class="space-y-6">
                <x-app-card class="rounded-[1.75rem] border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Fokus</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-950">Überfällige Positionen</h2>
                        </div>
                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">{{ $overdueCount }}</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($overdueInvoices->take(4) as $invoice)
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-950">#{{ $invoice->id }} · {{ $invoice->mieter->name }}</p>
                                        <p class="mt-1 text-sm text-slate-600">Fällig am {{ $invoice->faellig_am?->format('Y-m-d') ?? '—' }}</p>
                                    </div>
                                    <x-app-badge variant="red">{{ __('Invoice status overdue badge') }}</x-app-badge>
                                </div>
                            </div>
                        @empty
                            <x-app-empty-state :title="__('No open invoices.')" class="rounded-[1.25rem] border-dashed border-slate-200 bg-slate-50">
                                Alle Rechnungen sind aktuell im grünen Bereich.
                            </x-app-empty-state>
                        @endforelse
                    </div>
                </x-app-card>

                <x-app-card class="rounded-[1.75rem] border-slate-200 shadow-sm">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">{{ __('Dashboard KPI matching CTA title') }}</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-950">{{ __('Dashboard KPI matching CTA subtitle') }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Ordnen Sie neue Banktransaktionen zu und bereiten Sie offene Zahlungen für die Buchhaltung auf.</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ route('bank.matching') }}"
                            class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            {{ __('Go to matching') }}
                        </a>
                        <a
                            href="{{ route('bank.index') }}"
                            class="inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            {{ __('Bank import') }}
                        </a>
                    </div>
                </x-app-card>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <x-app-card class="overflow-hidden rounded-[1.75rem] border-slate-200 shadow-sm !p-0">
                <div class="border-b border-slate-200 px-5 py-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">{{ __('Recent activity section') }}</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-950">Letzte Zuordnungen</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('When') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Invoice') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Tenant') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($recentPayments as $z)
                                <tr class="transition-colors hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $z->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 font-medium text-slate-950">#{{ $z->rechnung_id }}</td>
                                    <td class="px-4 py-4 text-slate-700">{{ $z->rechnung->mieter->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums font-medium text-slate-950">{{ number_format($z->betrag_cent / 100, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10">
                                        <x-app-empty-state :title="__('No allocations yet.')" class="rounded-[1.25rem] border-dashed border-slate-200 bg-slate-50">
                                            {{ __('Dashboard activity empty hint') }}
                                        </x-app-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>

            <x-app-card class="overflow-hidden rounded-[1.75rem] border-slate-200 shadow-sm !p-0">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Leases</p>
                        <h2 id="leases-heading" class="mt-2 text-xl font-semibold text-slate-950">{{ __('Active leases — rent preview') }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ __('Amount from contract; actual invoices are separate snapshots when generated.') }}</p>
                    </div>
                    <a href="{{ route('mietvertraege.index') }}" class="shrink-0 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">{{ __('Manage leases') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Unit') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Tenant') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Next period hint') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Monthly rent (preview)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($leasePreviews as $row)
                                <tr class="transition-colors hover:bg-slate-50/80">
                                    <td class="px-4 py-4">
                                        <div>
                                            <p class="font-medium text-slate-950">{{ $row['lease']->einheit->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $row['lease']->einheit->objekt->name ?? '—' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-700">{{ $row['lease']->mieter->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $row['next_period_hint'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums font-medium text-slate-950">{{ number_format($row['total_cent'] / 100, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10">
                                        <x-app-empty-state :title="__('No active leases.')" class="rounded-[1.25rem] border-dashed border-slate-200 bg-slate-50" />
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
