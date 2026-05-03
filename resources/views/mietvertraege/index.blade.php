<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">{{ __('Leases') }}</h2>
            <a href="{{ route('mietvertraege.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ __('New lease') }}</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                @if (session('status') === 'lease-saved')
                    {{ __('Lease saved.') }}
                @elseif (session('status') === 'lease-updated')
                    {{ __('Lease updated.') }}
                @elseif (session('status') === 'lease-ended')
                    {{ __('Lease ended.') }}
                @endif
            </div>
        @endif
        <x-app-table>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Unit') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tenant') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Start') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('End') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($mietvertraege as $mv)
                        <tr class="transition-colors hover:bg-gray-50/80">
                            <td class="px-4 py-3 text-gray-950">{{ $mv->einheit->name }} / {{ $mv->einheit->objekt->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $mv->mieter->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $mv->starts_on->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $mv->ends_on?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($mv->status === \App\Models\Mietvertrag::STATUS_ACTIVE)
                                    <x-app-badge variant="green">{{ __('Contract status active') }}</x-app-badge>
                                @elseif ($mv->status === \App\Models\Mietvertrag::STATUS_DRAFT)
                                    <x-app-badge variant="gray">{{ __('Contract status draft') }}</x-app-badge>
                                @elseif ($mv->status === \App\Models\Mietvertrag::STATUS_SENT)
                                    <x-app-badge variant="amber">{{ __('Contract status sent') }}</x-app-badge>
                                @else
                                    <x-app-badge variant="gray">{{ $mv->status }}</x-app-badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-end">
                                @can('view', $mv)
                                    <a href="{{ route('mietvertraege.show', $mv) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('View') }}</a>
                                @endcan
                                @can('update', $mv)
                                    <a href="{{ route('mietvertraege.edit', $mv) }}" class="ms-4 text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Edit') }}</a>
                                @endcan
                                @can('end', $mv)
                                    <a href="{{ route('mietvertraege.end', $mv) }}" class="ms-4 text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('End') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-app-empty-state :title="__('No leases yet.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-100 px-4 py-3">{{ $mietvertraege->links() }}</div>
        </x-app-table>
    </div>
</x-app-layout>
