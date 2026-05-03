<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold leading-tight text-gray-950">{{ __('Lease detail') }}</h2>
            <div class="flex flex-wrap gap-3">
                @can('update', $mietvertrag)
                    <a href="{{ route('mietvertraege.edit', $mietvertrag) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                @endcan
                <a href="{{ route('mietvertraege.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-950">{{ __('Back to leases') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @if (session('status') === 'contract-pdf-generated')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ __('Contract PDF generated flash') }}</div>
        @endif
        @if (session('status') === 'contract-link-created')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ __('Contract link created flash') }}</div>
        @endif
        @if (session('status') === 'contract-activated-manual')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ __('Contract manual activate flash') }}</div>
        @endif

        <x-app-card>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-500">{{ __('Status') }}:</span>
                @if ($mietvertrag->isDraft())
                    <x-app-badge variant="gray">{{ __('Contract status draft') }}</x-app-badge>
                @elseif ($mietvertrag->isSent())
                    <x-app-badge variant="amber">{{ __('Contract status sent') }}</x-app-badge>
                @elseif ($mietvertrag->isActive())
                    <x-app-badge variant="green">{{ __('Contract status active') }}</x-app-badge>
                @else
                    <x-app-badge variant="gray">{{ $mietvertrag->status }}</x-app-badge>
                @endif
            </div>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-500">{{ __('Tenant') }}</dt><dd class="font-medium text-gray-950">{{ $mietvertrag->mieter->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Unit') }}</dt><dd class="font-medium text-gray-950">{{ $mietvertrag->einheit->name }} / {{ $mietvertrag->einheit->objekt->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Start date') }}</dt><dd>{{ $mietvertrag->starts_on->format('Y-m-d') }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Planned end (optional)') }}</dt><dd>{{ $mietvertrag->ends_on?->format('Y-m-d') ?? '—' }}</dd></div>
            </dl>
        </x-app-card>

        @if ($mietvertrag->status !== \App\Models\Mietvertrag::STATUS_ENDED)
            <x-app-card>
                <h3 class="text-base font-semibold text-gray-950">{{ __('Contract workflow section') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('Contract workflow hint') }}</p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="post" action="{{ route('mietvertraege.contract.pdf', $mietvertrag) }}">
                        @csrf
                        <x-primary-button type="submit">{{ __('Generate contract PDF') }}</x-primary-button>
                    </form>
                    <form method="post" action="{{ route('mietvertraege.contract.link', $mietvertrag) }}">
                        @csrf
                        <x-secondary-button type="submit">{{ __('Create tenant link') }}</x-secondary-button>
                    </form>
                    @if (in_array($mietvertrag->status, [\App\Models\Mietvertrag::STATUS_DRAFT, \App\Models\Mietvertrag::STATUS_SENT], true))
                        <form method="post" action="{{ route('mietvertraege.contract.activate', $mietvertrag) }}" onsubmit="return confirm(@json(__('Contract manual activate confirm')));">
                            @csrf
                            <x-secondary-button type="submit">{{ __('Activate contract manually') }}</x-secondary-button>
                        </form>
                    @endif
                </div>

                @if (session('contract_public_url'))
                    <div class="mt-6">
                        <p class="text-sm font-medium text-gray-700">{{ __('Tenant link URL label') }}</p>
                        <input type="text" readonly class="mt-2 w-full rounded-md border-gray-300 text-sm shadow-sm" value="{{ session('contract_public_url') }}" onclick="this.select()" />
                        <p class="mt-2 text-xs text-gray-500">{{ __('Tenant link URL help') }}</p>
                    </div>
                @endif
            </x-app-card>
        @endif

        <x-app-card>
            <h3 class="text-base font-semibold text-gray-950">{{ __('Contract event log') }}</h3>
            <ul class="mt-3 space-y-2 text-sm text-gray-700">
                @forelse ($mietvertrag->contractEvents as $ev)
                    <li class="border-b border-gray-100 pb-2">
                        <span class="font-medium text-gray-950">{{ $ev->event_type }}</span>
                        <span class="text-gray-500">· {{ $ev->actor_type }}</span>
                        <span class="text-gray-400">· {{ $ev->created_at->format('Y-m-d H:i') }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">{{ __('No contract events yet.') }}</li>
                @endforelse
            </ul>
        </x-app-card>
    </div>
</x-app-layout>
