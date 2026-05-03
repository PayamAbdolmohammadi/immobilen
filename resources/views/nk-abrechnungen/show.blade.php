<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Settlement') }} {{ $abrechnung->jahr }} — {{ $abrechnung->objekt->name }}</h2>
            <a href="{{ route('nk-abrechnungen.pdf', $abrechnung) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('Download PDF') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'nk-saved')
                <div class="p-4 bg-green-50 text-green-800 rounded-md text-sm">{{ __('Saved.') }}</div>
            @endif
            <div class="bg-white shadow sm:rounded-lg p-6 text-sm">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div><dt class="text-gray-500">{{ __('Distribution') }}</dt><dd>{{ $abrechnung->verteilungs_art }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Status') }}</dt><dd>{{ $abrechnung->status }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Units') }}</dt><dd>{{ $abrechnung->einheitenCount() }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Share per unit (preview)') }}</dt><dd>{{ number_format($abrechnung->anteilProEinheitCent() / 100, 2, ',', '.') }} €</dd></div>
                </dl>
                <h3 class="font-medium text-gray-900 mb-2">{{ __('Positions') }}</h3>
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pe-4">{{ __('Description') }}</th>
                            <th class="py-2 pe-4">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($abrechnung->positionen as $p)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pe-4">{{ $p->beschreibung }}</td>
                                <td class="py-2 pe-4">{{ number_format($p->betrag_cent / 100, 2, ',', '.') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold">
                            <td class="py-2 pe-4">{{ __('Total') }}</td>
                            <td class="py-2 pe-4">{{ number_format($abrechnung->totalCent() / 100, 2, ',', '.') }} €</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
