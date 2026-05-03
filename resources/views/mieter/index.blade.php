<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">{{ __('Tenants') }}</h2>
            <a href="{{ route('mieter.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ __('Add tenant') }}</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('status') === 'mieter-saved' || session('status') === 'mieter-deleted')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ __('Saved.') }}</div>
        @endif
        <x-app-table>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($mieter as $m)
                        <tr class="transition-colors hover:bg-gray-50/80">
                            <td class="px-4 py-3 font-medium text-gray-950">{{ $m->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $m->email ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-end">
                                <a href="{{ route('mieter.edit', $m) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">{{ __('Edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10">
                                <x-app-empty-state :title="__('No tenants yet.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-100 px-4 py-3">{{ $mieter->links() }}</div>
        </x-app-table>
    </div>
</x-app-layout>
