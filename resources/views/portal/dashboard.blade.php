@extends('layouts.portal')

@section('title', __('Overview'))

@section('content')
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 px-4 space-y-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h1 class="text-lg font-medium text-gray-900">{{ __('Hello, :name', ['name' => $mieter->name]) }}</h1>
            @if ($lease)
                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Property') }}</dt>
                        <dd>{{ $lease->einheit->objekt->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Unit') }}</dt>
                        <dd>{{ $lease->einheit->name }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-2 text-sm text-gray-600">{{ __('No active lease on file.') }}</p>
            @endif
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Open invoices') }}</h2>
            @if ($openInvoices->isEmpty())
                <p class="mt-2 text-sm text-gray-600">{{ __('You have no open invoices.') }}</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 pe-4">{{ __('Due') }}</th>
                                <th class="py-2 pe-4">{{ __('Amount') }}</th>
                                <th class="py-2 pe-4">{{ __('Open amount') }}</th>
                                <th class="py-2 pe-4">{{ __('Type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($openInvoices as $inv)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-4">{{ $inv->faellig_am?->format('Y-m-d') ?? '—' }}</td>
                                    <td class="py-2 pe-4">{{ number_format($inv->betrag_cent / 100, 2, ',', '.') }} €</td>
                                    <td class="py-2 pe-4">{{ number_format($inv->openAmountCent() / 100, 2, ',', '.') }} €</td>
                                    <td class="py-2 pe-4">{{ $inv->typ }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Documents') }}</h2>
            <p class="mt-2 text-sm text-gray-600">{{ __('Letters and uploads from your landlord will appear here when available.') }}</p>
        </div>
    </div>
@endsection
