@props(['title', 'subtitle' => null])

<div {{ $attributes->class(['flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        <h2 class="text-base font-semibold text-gray-950">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
