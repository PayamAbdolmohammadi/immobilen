<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Immobile') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-x-0 top-0 -z-10 h-[34rem] bg-gradient-to-b from-indigo-100 via-slate-50 to-slate-50"></div>
            <div class="absolute left-1/2 top-0 -z-10 h-80 w-80 -translate-x-1/2 rounded-full bg-violet-200/40 blur-3xl"></div>

            <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-10 px-4 py-8 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-12">
                <section class="order-2 rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-300/70 sm:p-8 lg:order-1 lg:p-10">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-white via-slate-200 to-indigo-200 text-sm font-semibold tracking-[0.18em] text-slate-950">
                            IM
                        </span>
                        <div>
                            <p class="text-base font-semibold">Immobile</p>
                            <p class="text-sm text-slate-400">Hausverwaltung einfach digital</p>
                        </div>
                    </div>

                    <div class="mt-10 max-w-xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-200">Registrierung</p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">
                            Digitale Hausverwaltung starten
                        </h1>
                        <p class="mt-5 text-lg leading-8 text-slate-300">
                            Erstellen Sie Ihr Konto, um Mietverträge, Abrechnungen, Bankimporte und Exportprozesse in einer Plattform aufzubauen.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-300 ring-1 ring-emerald-400/20">DSGVO-konform</span>
                        <span class="inline-flex items-center rounded-full bg-indigo-400/10 px-4 py-2 text-sm font-semibold text-indigo-200 ring-1 ring-indigo-400/20">DATEV-ready</span>
                        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-white/10">Made in Germany</span>
                    </div>

                    <div class="mt-10 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
                        <article class="rounded-[1.75rem] bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-slate-500">Setup-Übersicht</p>
                                    <h2 class="mt-1 text-xl font-semibold">Erster Verwaltungsprozess</h2>
                                </div>
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Neu</span>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm text-slate-500">Mandant anlegen</p>
                                            <p class="mt-1 font-semibold text-slate-950">Hausverwaltung Nord GmbH</p>
                                        </div>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Bereit</span>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm text-slate-500">Objektstruktur importieren</p>
                                            <p class="mt-1 font-semibold text-slate-950">12 Einheiten vorbereitet</p>
                                        </div>
                                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">Aktiv</span>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm text-slate-500">DATEV Export einrichten</p>
                                            <p class="mt-1 font-semibold text-slate-950">Monatsabschluss vorbereitet</p>
                                        </div>
                                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">Geplant</span>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <div class="space-y-5">
                            <article class="rounded-[1.75rem] bg-white/5 p-5 ring-1 ring-white/10">
                                <p class="text-sm text-slate-400">Monatsübersicht</p>
                                <div class="mt-4 grid gap-3">
                                    <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3 text-sm">
                                        <span class="text-slate-300">Offene Mietrechnungen</span>
                                        <span class="font-semibold text-white">124</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3 text-sm">
                                        <span class="text-slate-300">Bankimport bereit</span>
                                        <span class="font-semibold text-white">38</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3 text-sm">
                                        <span class="text-slate-300">DATEV Export vorbereitet</span>
                                        <span class="font-semibold text-white">1</span>
                                    </div>
                                </div>
                            </article>

                            <article class="rounded-[1.75rem] bg-white/5 p-5 ring-1 ring-white/10">
                                <p class="text-sm text-slate-400">Warum Immobile</p>
                                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                        Mietverträge mit PDFs und digitalen Bestätigungen.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-sky-400"></span>
                                        Wiederkehrende Mietabrechnung und Bankzuordnung an einem Ort.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-violet-400"></span>
                                        Saubere Übergabe an Buchhaltung und Steuerberatung.
                                    </li>
                                </ul>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="order-1 lg:order-2">
                    <div class="mx-auto w-full max-w-xl rounded-[2rem] bg-white p-6 shadow-2xl shadow-slate-200/80 ring-1 ring-slate-200 sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Konto erstellen</p>
                                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Registrieren</h2>
                            </div>
                            <a href="/" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">Zur Startseite</a>
                        </div>

                        <p class="mt-4 text-base leading-7 text-slate-600">
                            Richten Sie Ihr Konto in wenigen Schritten ein und starten Sie mit Ihrer digitalen Hausverwaltung.
                        </p>

                        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                            @csrf

                            <div>
                                <x-input-label for="name" class="text-sm font-medium text-slate-700" value="Name" />
                                <x-text-input
                                    id="name"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="Max Mustermann"
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-rose-600" />
                            </div>

                            <div>
                                <x-input-label for="mandanten_name" class="text-sm font-medium text-slate-700" value="Mandant / Firma (optional)" />
                                <x-text-input
                                    id="mandanten_name"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                    type="text"
                                    name="mandanten_name"
                                    :value="old('mandanten_name')"
                                    maxlength="255"
                                    autocomplete="organization"
                                    placeholder="Hausverwaltung Nord GmbH"
                                />
                                <x-input-error :messages="$errors->get('mandanten_name')" class="mt-2 text-sm text-rose-600" />
                                <p class="mt-2 text-xs text-slate-500">Leerlassen verwendet Ihren Anzeigenamen.</p>
                            </div>

                            <div>
                                <x-input-label for="email" class="text-sm font-medium text-slate-700" :value="__('Email')" />
                                <x-text-input
                                    id="email"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autocomplete="username"
                                    placeholder="name@unternehmen.de"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-rose-600" />
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="password" class="text-sm font-medium text-slate-700" value="Passwort" />
                                    <x-text-input
                                        id="password"
                                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Mindestens 8 Zeichen"
                                    />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-600" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" class="text-sm font-medium text-slate-700" value="Passwort bestätigen" />
                                    <x-text-input
                                        id="password_confirmation"
                                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Passwort wiederholen"
                                    />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-rose-600" />
                                </div>
                            </div>

                            <x-primary-button class="flex min-h-12 w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-600 focus:ring-indigo-500">
                                Kostenlos registrieren
                            </x-primary-button>

                            <p class="text-center text-sm text-slate-500">
                                Bereits registriert?
                                <a
                                    href="{{ route('login') }}"
                                    class="font-semibold text-indigo-600 transition hover:text-indigo-700"
                                >
                                    Jetzt anmelden
                                </a>
                            </p>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
