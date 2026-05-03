<div class="flex h-16 shrink-0 items-center gap-2 border-b border-gray-100 px-4">
    <a href="{{ route('dashboard') }}" class="flex min-w-0 flex-1 items-center gap-2 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
        <x-application-logo class="h-8 w-auto shrink-0 fill-current text-gray-900" />
        <span class="truncate text-sm font-semibold text-gray-950">{{ config('app.name', 'ImmoMandat') }}</span>
    </a>
    <button
        type="button"
        class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 lg:hidden"
        @click="sidebarOpen = false"
    >
        <span class="sr-only">{{ __('Close navigation') }}</span>
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="{{ __('Primary navigation') }}">
    <div>
        <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Nav group main') }}</p>
        <div class="space-y-0.5">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-sidebar-link>
            @if (auth()->user()?->isOwner())
                <x-sidebar-link :href="route('demo-flow.index')" :active="request()->routeIs('demo-flow.*')">
                    {{ __('WOW demo') }}
                </x-sidebar-link>
            @endif
        </div>
    </div>

    <div>
        <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Nav group verwaltung') }}</p>
        <div class="space-y-0.5">
            <x-sidebar-link :href="route('mieter.index')" :active="request()->routeIs('mieter.*')">
                {{ __('Tenants') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('objekte.index')" :active="request()->routeIs('objekte.*')">
                {{ __('Properties') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('einheiten.index')" :active="request()->routeIs('einheiten.*')">
                {{ __('Units') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('mietvertraege.index')" :active="request()->routeIs('mietvertraege.*')">
                {{ __('Leases') }}
            </x-sidebar-link>
        </div>
    </div>

    <div>
        <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Nav group finanzen') }}</p>
        <div class="space-y-0.5">
            <x-sidebar-link :href="route('manual-rechnungen.index')" :active="request()->routeIs('manual-rechnungen.*')">
                {{ __('Invoices') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('bank.index')" :active="request()->routeIs('bank.index')">
                {{ __('Bank import') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('bank.matching')" :active="request()->routeIs('bank.matching')">
                {{ __('Nav allocate payments') }}
            </x-sidebar-link>
            @if (auth()->user()?->isOwner())
                <x-sidebar-link :href="route('export.accounting.index')" :active="request()->routeIs('export.accounting.*')">
                    {{ __('Accounting / Export') }}
                </x-sidebar-link>
            @endif
        </div>
    </div>

    <div>
        <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Nav group abrechnung') }}</p>
        <div class="space-y-0.5">
            <x-sidebar-link :href="route('nk-abrechnungen.index')" :active="request()->routeIs('nk-abrechnungen.*')">
                {{ __('NK / Year-end') }}
            </x-sidebar-link>
        </div>
    </div>
</nav>
