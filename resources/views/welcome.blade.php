<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
        @if (Route::has('login'))
            <header class="border-b border-gray-200 bg-white">
                <nav class="mx-auto flex max-w-5xl flex-wrap items-center justify-end gap-2 px-4 py-4 text-sm">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="rounded-md px-4 py-2 font-medium text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50 hover:text-gray-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md px-4 py-2 font-medium text-gray-700 transition hover:text-gray-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('portal.login'))
                            <a
                                href="{{ route('portal.login') }}"
                                class="rounded-md px-4 py-2 font-medium text-gray-700 transition hover:text-gray-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            >
                                {{ __('Tenant portal') }}
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                            >
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            </header>
        @endif

        <main class="mx-auto max-w-lg px-4 py-16 text-center sm:py-24">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-950 sm:text-3xl">
                {{ __('Welcome landing headline') }}
            </h1>
            <p class="mt-4 text-base leading-relaxed text-gray-600">
                {{ __('Welcome landing intro') }}
            </p>
            @guest
                @if (Route::has('login') && Route::has('register'))
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        >
                            {{ __('Welcome landing CTA login') }}
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-md px-5 py-2.5 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        >
                            {{ __('Welcome landing CTA register') }}
                        </a>
                    </div>
                @endif
            @endguest
        </main>
    </body>
</html>
