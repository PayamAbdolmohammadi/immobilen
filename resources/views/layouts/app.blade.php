<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }">
            {{-- Mobile backdrop --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity.duration.200ms
                class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden"
                style="display: none;"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Sidebar --}}
            <aside
                id="app-sidebar"
                class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-out lg:static lg:z-0 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
                aria-label="{{ __('App sidebar') }}"
            >
                @include('layouts.sidebar')
            </aside>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 lg:px-6">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 lg:hidden"
                        @click="sidebarOpen = true"
                        :aria-expanded="sidebarOpen ? 'true' : 'false'"
                        aria-controls="app-sidebar"
                    >
                        <span class="sr-only">{{ __('Open navigation') }}</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        @isset($header)
                            <div class="text-gray-950 [&_h2]:text-lg [&_h2]:font-semibold [&_h2]:tracking-tight sm:[&_h2]:text-xl">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    @auth
                        @if (auth()->user()?->mandant)
                            <span class="hidden max-w-[10rem] truncate text-xs text-gray-500 xl:inline" title="{{ auth()->user()->mandant->name }}">
                                {{ auth()->user()->mandant->name }}
                            </span>
                        @endif
                        <span class="hidden rounded-md bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-700 sm:inline">
                            {{ auth()->user()?->isOwner() ? __('Role owner badge') : __('Role staff badge') }}
                        </span>
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex max-w-[12rem] items-center gap-1 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                >
                                    <span class="truncate">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @endauth
                </header>

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
