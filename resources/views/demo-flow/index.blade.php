<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">{{ __('WOW demo page title') }}</h2>
    </x-slot>

    @php
        $btnPrimary = 'inline-flex items-center justify-center rounded-md bg-indigo-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition';
        $btnSecondary = 'inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1 transition';
    @endphp

    <div class="py-6 sm:py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero --}}
            <section class="text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('WOW demo badge') }}</p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-gray-950 tracking-tight">
                    {{ __('WOW headline rent runs') }}
                </h1>
                <p class="mt-3 text-sm sm:text-base text-gray-600 max-w-xl mx-auto leading-relaxed">
                    {{ __('WOW subheadline full flow') }}
                </p>
                <form method="post" action="{{ route('demo-flow.start-auto') }}" class="mt-8 flex justify-center">
                    @csrf
                    <button type="submit" class="{{ $btnPrimary }} w-full max-w-xs sm:w-auto sm:min-w-[220px]">
                        {{ __('WOW CTA start demo') }}
                    </button>
                </form>
            </section>

            {{-- Flow visualization --}}
            <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-xs font-medium text-gray-600 shadow-sm">
                <span class="text-gray-800">{{ __('Flow viz lease') }}</span>
                <span class="text-gray-400" aria-hidden="true">{{ __('Flow viz arrow') }}</span>
                <span>{{ __('Flow viz invoice') }}</span>
                <span class="text-gray-400" aria-hidden="true">{{ __('Flow viz arrow') }}</span>
                <span>{{ __('Flow viz payment') }}</span>
                <span class="text-gray-400" aria-hidden="true">{{ __('Flow viz arrow') }}</span>
                <span>{{ __('Flow viz match') }}</span>
                <span class="text-gray-400" aria-hidden="true">{{ __('Flow viz arrow') }}</span>
                <span>{{ __('Flow viz export') }}</span>
            </div>

            {{-- Flash messages --}}
            @if (session('status') === 'demo-auto-prepared')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-900 text-sm" role="status">
                    <p class="font-semibold">{{ __('Demo prepared title') }}</p>
                    <p class="mt-1 text-green-800 leading-snug">
                        @if ((int) session('demo_auto_rent_created', 0) > 0)
                            {{ __('Demo prepared detail rent yes') }}
                        @else
                            {{ __('Demo prepared detail rent no') }}
                        @endif
                        @if (session('demo_auto_bank_created'))
                            {{ __('Demo prepared detail bank yes') }}
                        @else
                            {{ __('Demo prepared detail bank no') }}
                        @endif
                    </p>
                </div>
            @endif

            @if (session('status') === 'demo-payment-matched')
                <div class="rounded-md border-2 border-green-400 bg-green-100 px-4 py-4 text-green-950 shadow-sm" role="alert">
                    <p class="text-base font-bold">{{ __('Demo payment matched success') }}</p>
                </div>
            @endif

            @if (session('status') === 'demo-rent-generated')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-900 text-sm">
                    @if ((int) session('demo_rent_created', 0) === 0)
                        {{ __('No new rent invoice was created (already exists for this period or no eligible lease).') }}
                    @else
                        {{ __(':count rent invoice(s) created for :period.', ['count' => (int) session('demo_rent_created', 0), 'period' => session('demo_rent_period', '')]) }}
                    @endif
                </div>
            @endif

            @if (session('status') === 'demo-bank-seeded')
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-900 text-sm">
                    {{ __('Demo bank line created. Continue with bank matching.') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-900 text-sm">
                    {{ $errors->first('demo_auto') ?: $errors->first('period') ?: $errors->first() }}
                </div>
            @endif

            @if ($hasActiveLease)
                <p class="text-center text-xs text-gray-500 leading-snug">{{ __('Expected monthly rent for CSV / demo bank line: :amount € (first active lease).', ['amount' => $expectedRentEuroFormatted]) }}</p>
            @else
                <p class="text-center text-xs text-amber-700 leading-snug">{{ __('No active lease yet — create a lease first, or use seeded demo data (demo@example.com).') }}</p>
            @endif

            {{-- Story steps --}}
            <div class="space-y-4">
                {{-- Step 1 --}}
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">1</span>
                        <div class="min-w-0 flex-1 space-y-2">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('WOW story step 1 title') }}</h3>
                            <p class="text-sm text-gray-600 leading-snug">{{ __('WOW story step 1 body') }}</p>
                            <div class="pt-1">
                                <a href="{{ route('mieter.index') }}" class="{{ $btnSecondary }}">{{ __('WOW link tenants') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">2</span>
                        <div class="min-w-0 flex-1 space-y-2">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('WOW story step 2 title') }}</h3>
                            <p class="text-sm text-gray-600 leading-snug">{{ __('WOW story step 2 body') }}</p>
                            <div class="pt-1">
                                <a href="{{ route('mietvertraege.index') }}" class="{{ $btnSecondary }}">{{ __('WOW link leases') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 — core value --}}
                <div class="rounded-lg border-2 border-indigo-300 bg-indigo-50/80 p-5 shadow-md">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">3</span>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">{{ __('WOW story step 3 title') }}</h3>
                                <p class="mt-1 text-sm text-gray-700 leading-snug">{{ __('WOW story step 3 body') }}</p>
                                <p class="mt-2 text-sm font-medium text-indigo-800">{{ __('WOW step 3 helper one click') }}</p>
                            </div>
                            <form method="post" action="{{ route('demo-flow.generate-rent') }}" class="block">
                                @csrf
                                <input type="hidden" name="period" value="{{ $billingPeriod }}" />
                                <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto">
                                    {{ __('WOW step 3 primary button') }}
                                </button>
                            </form>
                            <p class="text-xs text-gray-600">{{ __('Current period: :period', ['period' => $billingPeriod]) }}</p>
                            <a href="{{ route('dashboard') }}" class="inline-block text-sm font-medium text-indigo-700 underline underline-offset-2 hover:text-indigo-900">{{ __('WOW link dashboard invoices') }}</a>
                        </div>
                    </div>
                </div>

                {{-- Step 4 bank --}}
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">4</span>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('WOW story step 4 title') }}</h3>
                                <p class="mt-1 text-sm text-gray-600 leading-snug">{{ __('WOW story step 4 body') }}</p>
                                <h4 class="mt-3 text-sm font-semibold text-gray-900">{{ __('WOW demo payment section title') }}</h4>
                                <p class="mt-1 text-sm text-gray-600 leading-snug">{{ __('WOW demo payment section description') }}</p>
                            </div>
                            <div class="flex flex-col sm:flex-row flex-wrap gap-2 pt-1">
                                <form method="post" action="{{ route('demo-flow.seed-bank-line') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto">{{ __('WOW demo payment button') }}</button>
                                </form>
                                <a href="{{ route('demo-flow.demo-bank-csv') }}" class="{{ $btnSecondary }} w-full sm:w-auto">{{ __('Download demo bank CSV') }}</a>
                                <a href="{{ route('bank.index', ['demo' => 1]) }}" class="{{ $btnSecondary }} w-full sm:w-auto">{{ __('WOW link bank import') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 5 matching --}}
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">5</span>
                        <div class="min-w-0 flex-1 space-y-2">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('WOW story step 5 title') }}</h3>
                            <p class="text-sm text-gray-600 leading-snug">{{ __('WOW story step 5 body') }}</p>
                            <div class="pt-1">
                                <a href="{{ route('bank.matching', ['demo' => 1]) }}" class="{{ $btnPrimary }} w-full sm:w-auto">{{ __('WOW link matching prominent') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 6 export --}}
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-3 text-left">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">6</span>
                        <div class="min-w-0 flex-1 space-y-2">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('WOW story step 6 title') }}</h3>
                            <p class="text-sm text-gray-600 leading-snug">{{ __('WOW story step 6 body') }}</p>
                            <div class="pt-1">
                                <a href="{{ route('export.accounting.index') }}" class="{{ $btnSecondary }}">{{ __('WOW link accounting export') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick navigation --}}
            <section class="border-t border-gray-200 pt-6">
                <p class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('WOW quick nav label') }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('mietvertraege.index') }}" class="{{ $btnSecondary }} text-sm">{{ __('WOW quick leases') }}</a>
                    <a href="{{ route('bank.matching', ['demo' => 1]) }}" class="{{ $btnSecondary }} text-sm">{{ __('WOW quick matching') }}</a>
                    <a href="{{ route('export.accounting.index') }}" class="{{ $btnSecondary }} text-sm">{{ __('WOW quick export') }}</a>
                </div>
            </section>

            {{-- Result box --}}
            <section class="rounded-lg border border-green-200 bg-green-50 p-5 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">{{ __('WOW result title') }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-800 leading-snug">
                    <li class="flex gap-2"><span class="shrink-0 text-green-700 font-bold">•</span>{{ __('WOW result line 1') }}</li>
                    <li class="flex gap-2"><span class="shrink-0 text-green-700 font-bold">•</span>{{ __('WOW result line 2') }}</li>
                    <li class="flex gap-2"><span class="shrink-0 text-green-700 font-bold">•</span>{{ __('WOW result line 3') }}</li>
                    <li class="flex gap-2"><span class="shrink-0 text-green-700 font-bold">•</span>{{ __('WOW result line 4') }}</li>
                </ul>
                <p class="mt-5 text-base font-bold text-gray-900">{{ __('WOW result tagline') }}</p>
            </section>
        </div>
    </div>
</x-app-layout>
