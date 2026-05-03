@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    @class([
        'flex items-center rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white',
        'bg-indigo-50 text-indigo-900 ring-1 ring-inset ring-indigo-100' => $active,
        'text-gray-700 hover:bg-gray-50' => ! $active,
    ])
    @if ($active) aria-current="page" @endif
    {{ $attributes }}
>
    {{ $slot }}
</a>
