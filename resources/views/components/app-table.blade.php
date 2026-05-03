@props([])

<x-app-card :padding="false" {{ $attributes }}>
    @isset($title)
        <div class="border-b border-gray-100 px-5 py-4">
            <h3 class="text-base font-semibold text-gray-950">{{ $title }}</h3>
        </div>
    @endisset
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</x-app-card>
