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
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-200">Anmeldung</p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">
                            Willkommen zurück
                        </h1>
                        <p class="mt-5 text-lg leading-8 text-slate-300">
                            Melden Sie sich an, um Mietverträge, Abrechnungen und Bankimporte zentral zu verwalten.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-300 ring-1 ring-emerald-400/20">DSGVO-konform</span>
                        <span class="inline-flex items-center rounded-full bg-indigo-400/10 px-4 py-2 text-sm font-semibold text-indigo-200 ring-1 ring-indigo-400/20">DATEV-ready</span>
                        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-white/10">Made in Germany</span>
                    </div>

                    <div class="mt-10 grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                        <article class="rounded-[1.75rem] bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-slate-500">Monatsübersicht</p>
                                    <h2 class="mt-1 text-xl font-semibold">Verwaltungs-Cockpit</h2>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktiv</span>
                            </div>

                            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Offene Mietrechnungen</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-950">124</p>
                                    <p class="mt-2 text-xs font-medium text-amber-700">7 mit Klärungsbedarf</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Bankimport bereit</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-950">38</p>
                                    <p class="mt-2 text-xs font-medium text-sky-700">12 Zahlungen erkannt</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">DATEV Export</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-950">1</p>
                                    <p class="mt-2 text-xs font-medium text-violet-700">vorbereitet</p>
                                </div>
                            </div>

                            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="grid grid-cols-[1.2fr_0.9fr_0.8fr] bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                    <span>Mieter</span>
                                    <span>Betrag</span>
                                    <span>Status</span>
                                </div>
                                <div class="divide-y divide-slate-200">
                                    <div class="grid grid-cols-[1.2fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                        <div>
                                            <p class="font-medium text-slate-950">Anna Schubert</p>
                                            <p class="text-slate-500">Berliner Allee 12</p>
                                        </div>
                                        <div class="font-medium text-slate-700">850,00 EUR</div>
                                        <div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Bezahlt</span></div>
                                    </div>
                                    <div class="grid grid-cols-[1.2fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                        <div>
                                            <p class="font-medium text-slate-950">Markus Weber</p>
                                            <p class="text-slate-500">Hofgarten 8</p>
                                        </div>
                                        <div class="font-medium text-slate-700">1.240,00 EUR</div>
                                        <div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Offen</span></div>
                                    </div>
                                    <div class="grid grid-cols-[1.2fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                        <div>
                                            <p class="font-medium text-slate-950">Sabine Keller</p>
                                            <p class="text-slate-500">Rheinblick 3</p>
                                        </div>
                                        <div class="font-medium text-slate-700">970,00 EUR</div>
                                        <div><span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">Bereit</span></div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <div class="space-y-5">
                            <article class="rounded-[1.75rem] bg-white/5 p-5 ring-1 ring-white/10">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm text-slate-400">Bankimport</p>
                                        <h3 class="mt-1 text-lg font-semibold">Zuordnung läuft</h3>
                                    </div>
                                    <span class="rounded-full bg-sky-400/10 px-3 py-1 text-xs font-semibold text-sky-300">Bereit</span>
                                </div>
                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full w-3/4 rounded-full bg-gradient-to-r from-sky-400 to-indigo-400"></div>
                                </div>
                                <p class="mt-4 text-sm text-slate-300">SEPA-Umsätze wurden importiert und den offenen Posten zugeordnet.</p>
                            </article>

                            <article class="rounded-[1.75rem] bg-white/5 p-5 ring-1 ring-white/10">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm text-slate-400">DATEV Export vorbereitet</p>
                                        <h3 class="mt-1 text-lg font-semibold">Abschluss April 2026</h3>
                                    </div>
                                    <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-semibold text-violet-300">Bereit</span>
                                </div>
                                <div class="mt-4 space-y-3 text-sm text-slate-300">
                                    <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                                        <span>Belege vorbereitet</span>
                                        <span class="font-semibold text-white">218</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                                        <span>Mandanten geprüft</span>
                                        <span class="font-semibold text-white">4 / 4</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="order-1 lg:order-2">
                    <div class="mx-auto w-full max-w-xl rounded-[2rem] bg-white p-6 shadow-2xl shadow-slate-200/80 ring-1 ring-slate-200 sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Login</p>
                                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Anmelden</h2>
                            </div>
                            <a href="/" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">Zur Startseite</a>
                        </div>

                        <p class="mt-4 text-base leading-7 text-slate-600">
                            Greifen Sie auf Verträge, Rechnungen, Bankabgleich und Exporte in Ihrer zentralen Verwaltungsoberfläche zu.
                        </p>

                        <x-auth-session-status
                            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
                            :status="session('status')"
                        />

                        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="email" class="text-sm font-medium text-slate-700" :value="__('Email')" />
                                <x-text-input
                                    id="email"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="name@unternehmen.de"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-rose-600" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-4">
                                    <x-input-label for="password" class="text-sm font-medium text-slate-700" value="Passwort" />
                                    @if (Route::has('password.request'))
                                        <a
                                            class="text-sm font-medium text-slate-500 transition hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md"
                                            href="{{ route('password.request') }}"
                                        >
                                            Passwort vergessen?
                                        </a>
                                    @endif
                                </div>

                                <x-text-input
                                    id="password"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Ihr Passwort"
                                />

                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-600" />
                            </div>

                            <label for="remember_me" class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    name="remember"
                                    @checked(old('remember'))
                                >
                                <span>Angemeldet bleiben</span>
                            </label>

                            <x-primary-button class="flex min-h-12 w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-600 focus:ring-indigo-500">
                                Anmelden
                            </x-primary-button>

                            @if (Route::has('register'))
                                <p class="text-center text-sm text-slate-500">
                                    Noch kein Konto?
                                    <a
                                        href="{{ route('register') }}"
                                        class="font-semibold text-indigo-600 transition hover:text-indigo-700"
                                    >
                                        Kostenlos registrieren
                                    </a>
                                </p>
                            @endif
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
