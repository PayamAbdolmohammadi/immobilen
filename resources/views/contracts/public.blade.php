<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Contract public page title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="mx-auto max-w-lg px-4 py-10">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-lg font-semibold text-gray-950">{{ __('Contract public heading') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('Contract public intro') }}</p>

            @if (session('status') === 'contract-accepted')
                <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-900" role="status">
                    {{ __('Contract accepted success') }}
                </div>
            @endif
            @if (session('status') === 'contract-already-accepted')
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800" role="status">
                    {{ __('Contract already accepted') }}
                </div>
            @endif

            <dl class="mt-6 space-y-2 text-sm">
                <div><dt class="text-gray-500">{{ __('Tenant') }}</dt><dd class="font-medium text-gray-950">{{ $vertrag->mieter->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Unit') }}</dt><dd class="font-medium text-gray-950">{{ $vertrag->einheit->name }} — {{ $vertrag->einheit->objekt->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Start date') }}</dt><dd>{{ $vertrag->starts_on->format('Y-m-d') }}</dd></div>
            </dl>

            @if ($vertrag->contract_pdf_path)
                <div class="mt-6">
                    <a href="{{ route('contracts.public.pdf', ['token' => $token]) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ __('Download contract PDF') }}
                    </a>
                </div>
            @else
                <p class="mt-6 text-sm text-amber-800">{{ __('Contract PDF not yet available') }}</p>
            @endif

            <p class="mt-6 text-xs text-gray-500">{{ __('Contract legal placeholder note') }}</p>

            @if (in_array($vertrag->status, [\App\Models\Mietvertrag::STATUS_DRAFT, \App\Models\Mietvertrag::STATUS_SENT], true))
                <form method="post" action="{{ route('contracts.public.accept', ['token' => $token]) }}" class="mt-8 space-y-4 border-t border-gray-100 pt-6">
                    @csrf
                    <label class="flex items-start gap-2 text-sm text-gray-800">
                        <input type="checkbox" name="accepted" value="1" class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" required />
                        <span>{{ __('Contract accept checkbox') }}</span>
                    </label>
                    <x-input-error class="mt-1" :messages="$errors->get('accepted')" />
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                        {{ __('Contract accept button') }}
                    </button>
                </form>
            @elseif ($vertrag->isActive())
                <p class="mt-8 border-t border-gray-100 pt-6 text-sm font-medium text-gray-900">{{ __('Contract already active tenant message') }}</p>
            @endif
        </div>
    </div>
</body>
</html>
