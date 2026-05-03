@props(['title'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-gray-200 bg-gray-50/80 px-6 py-10 text-center']) }}>
    <p class="text-sm font-medium text-gray-950">{{ $title }}</p>
    @if (isset($slot) && $slot->isNotEmpty())
        <p class="mt-2 text-sm text-gray-600">{{ $slot }}</p>
    @endif
</div>
