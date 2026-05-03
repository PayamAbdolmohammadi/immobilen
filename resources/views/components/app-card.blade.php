@props([
    'padding' => true,
])

<div {{ $attributes->class([
    'rounded-xl border border-gray-200 bg-white shadow-sm',
    'p-5 sm:p-6' => $padding,
]) }}>
    {{ $slot }}
</div>
