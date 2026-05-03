@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'neutral' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'indigo' => 'bg-indigo-50 text-indigo-800 ring-indigo-100',
        'amber' => 'bg-amber-50 text-amber-900 ring-amber-100',
        'green' => 'bg-green-50 text-green-800 ring-green-100',
        'red' => 'bg-red-50 text-red-800 ring-red-100',
        'gray' => 'bg-gray-100 text-gray-600 ring-gray-200',
    ];
    $cls = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
    $cls,
]) }}>
    {{ $slot }}
</span>
