<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">
                {{ __('Manual invoices') }}
            </h2>
            <a href="{{ route('manual-rechnungen.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('New invoice') }}
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                @if (session('status') === 'rechnung-created')
                    {{ __('Invoice created.') }}
                @elseif (session('status') === 'rechnung-updated')
                    {{ __('Invoice updated.') }}
                @elseif (session('status') === 'rechnung-deleted')
                    {{ __('Invoice deleted.') }}
                @endif
            </div>
        @endif

        <x-app-table>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tenant') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Unit') }}</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Amount') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Due') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($rechnungen as $rechnung)
                        <tr class="transition-colors hover:bg-gray-50/80">
                            <td class="px-4 py-3 font-medium text-gray-950">{{ $rechnung->mieter->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $rechnung->einheit?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-gray-950">{{ number_format($rechnung->betrag_cent / 100, 2, ',', '.') }} €</td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $rechnung->faellig_am?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @switch($rechnung->status)
                                    @case(\App\Models\Rechnung::STATUS_OFFEN)
                                        <x-app-badge variant="amber">{{ __('open') }}</x-app-badge>
                                        @break
                                    @case(\App\Models\Rechnung::STATUS_BEZAHLT)
                                        <x-app-badge variant="green">{{ __('Paid') }}</x-app-badge>
                                        @break
                                    @case(\App\Models\Rechnung::STATUS_STORNIERT)
                                        <x-app-badge variant="red">{{ __('Storno') }}</x-app-badge>
                                        @break
                                    @default
                                        <x-app-badge variant="gray">{{ $rechnung->status }}</x-app-badge>
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-end">
                                <a href="{{ route('manual-rechnungen.edit', $rechnung) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-app-empty-state :title="__('No manual invoices yet.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-100 px-4 py-3">{{ $rechnungen->links() }}</div>
        </x-app-table>
    </div>
</x-app-layout>
