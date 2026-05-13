<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <meta
            name="description"
            content="Immobile ist die digitale Plattform fur Hausverwaltungen: Mietvertrage, wiederkehrende Rechnungen, Bankimport, Mahnwesen, DATEV-ready Export und Mieterportal in einer Anwendung."
        >
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        @php
            $featureCards = [
                [
                    'title' => 'Mietvertrage & PDFs',
                    'description' => 'Vertrage erstellen, PDF-Dokumente ausgeben und Bestatigungen ohne Medienbruch direkt im Prozess verwalten.',
                    'meta' => 'Vertrag, PDF, Bestatigung',
                    'icon' => 'document',
                ],
                [
                    'title' => 'Automatische Mietabrechnung',
                    'description' => 'Wiederkehrende Mietrechnungen laufen planbar durch und bleiben fur Teams und Mandanten jederzeit nachvollziehbar.',
                    'meta' => 'Wiederkehrend, planbar',
                    'icon' => 'invoice',
                ],
                [
                    'title' => 'Bankabgleich',
                    'description' => 'Importieren Sie Umsatze, ordnen Sie Zahlungen zu und behalten Sie offene Posten ohne manuelle Listen im Blick.',
                    'meta' => 'Import, Zuordnung, Status',
                    'icon' => 'bank',
                ],
                [
                    'title' => 'DATEV-ready Export',
                    'description' => 'Bereiten Sie Buchhaltungsdaten strukturiert fur Steuerberatung und Finanzteam auf, ohne Zusatztool und Umwege.',
                    'meta' => 'Saubere Ubergabe',
                    'icon' => 'datev',
                ],
                [
                    'title' => 'Mieterportal',
                    'description' => 'Dokumente, Nachrichten und Rechnungen fur Mieter digital bereitstellen und professionell auffindbar machen.',
                    'meta' => 'Digitaler Zugriff',
                    'icon' => 'portal',
                ],
                [
                    'title' => 'Mahnwesen',
                    'description' => 'Offene Forderungen erkennen, Prozesse sauber nachhalten und weitere Schritte im selben System koordinieren.',
                    'meta' => 'Offene Posten im Griff',
                    'icon' => 'warning',
                ],
            ];

            $workflowSteps = [
                [
                    'number' => '01',
                    'title' => 'Vertrag erstellen',
                    'description' => 'Mietvertrag aufsetzen, PDF erzeugen und digital freigeben.',
                ],
                [
                    'number' => '02',
                    'title' => 'Miete automatisch abrechnen',
                    'description' => 'Wiederkehrende Rechnungen werden ohne manuelle Routine erstellt.',
                ],
                [
                    'number' => '03',
                    'title' => 'Zahlungen zuordnen',
                    'description' => 'Bankimporte abgleichen und offene Posten schneller schliessen.',
                ],
                [
                    'number' => '04',
                    'title' => 'Export fur Buchhaltung vorbereiten',
                    'description' => 'DATEV-ready Daten fur den nachgelagerten Finanzprozess bereitstellen.',
                ],
            ];

            $footerLinks = [
                ['label' => 'Funktionen', 'href' => '#funktionen'],
                ['label' => 'Workflow', 'href' => '#workflow'],
                ['label' => 'Mieterportal', 'href' => '#mieterportal'],
                ['label' => 'Preise', 'href' => '#preise'],
            ];
        @endphp

        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-x-0 top-0 -z-10 h-[40rem] bg-gradient-to-b from-indigo-100 via-slate-50 to-slate-50"></div>
            <div class="absolute left-1/2 top-0 -z-10 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-violet-200/40 blur-3xl"></div>

            <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/85 backdrop-blur">
                <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="/" class="flex min-w-0 items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-700 text-sm font-semibold tracking-[0.18em] text-white shadow-lg shadow-indigo-500/20">
                            IM
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-base font-semibold text-slate-950">Immobile</span>
                            <span class="block truncate text-xs text-slate-500">Hausverwaltung einfach digital</span>
                        </span>
                    </a>

                    <div class="hidden items-center gap-1 rounded-full border border-slate-200 bg-white/80 p-1 lg:flex">
                        <a href="#funktionen" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Funktionen</a>
                        <a href="#workflow" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Workflow</a>
                        <a href="#mieterportal" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Mieterportal</a>
                        <a href="#preise" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Preise</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="hidden rounded-full px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 sm:inline-flex"
                            >
                                Dashboard
                            </a>
                        @else
                            @if (Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    class="hidden rounded-full px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 sm:inline-flex"
                                >
                                    Anmelden
                                </a>
                            @endif
                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                >
                                    Kostenlos testen
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            </header>

            <main>
                <section
                    id="hero"
                    class="relative flex min-h-[100svh] w-full flex-col justify-center overflow-hidden py-20 sm:py-24 lg:py-28"
                    aria-labelledby="hero-heading"
                >
                    <div class="pointer-events-none absolute inset-0 z-[1] min-h-[100svh] w-full overflow-hidden" aria-hidden="true">
                        <video
                            class="absolute inset-0 z-[1] h-full w-full object-cover"
                            src="{{ asset('videos/hero.mp4') }}"
                            autoplay
                            muted
                            loop
                            playsinline
                            preload="auto"
                            aria-hidden="true"
                        >
                            <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4" />
                        </video>
                        {{-- Single readable overlay so the picture stays visible --}}
                        <div class="absolute inset-0 z-[2] bg-gradient-to-b from-slate-950/45 via-slate-950/55 to-slate-950/80"></div>
                        <div class="absolute inset-0 z-[2] bg-gradient-to-r from-slate-950/70 via-slate-950/20 to-transparent"></div>
                    </div>

                    <div class="relative z-[3] mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.98fr)] lg:items-center lg:gap-16">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap gap-3">
                                <span class="inline-flex items-center rounded-full border border-emerald-400/35 bg-emerald-500/15 px-4 py-2 text-sm font-semibold text-emerald-100 backdrop-blur-sm">DSGVO-konform</span>
                                <span class="inline-flex items-center rounded-full border border-indigo-400/35 bg-indigo-500/15 px-4 py-2 text-sm font-semibold text-indigo-100 backdrop-blur-sm">DATEV-ready</span>
                                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">Made in Germany</span>
                            </div>

                            <h1 id="hero-heading" class="mt-8 text-4xl font-semibold tracking-tight text-white sm:text-5xl sm:leading-[1.08] lg:text-6xl lg:leading-[1.06]">
                                Hausverwaltung, Mietabrechnung und Mieterportal in einer Plattform.
                            </h1>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl sm:leading-9">
                                Verwalten Sie Mietvertrage, wiederkehrende Rechnungen, Bankimporte und DATEV-ready Exporte strukturiert, nachvollziehbar und fur deutsche Hausverwaltungen entwickelt.
                            </p>

                            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex min-h-12 items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-indigo-950/40 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                                    >
                                        Kostenlos testen
                                    </a>
                                @endif
                                <a
                                    href="#workflow"
                                    class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/25 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white shadow-sm backdrop-blur-sm transition duration-200 hover:-translate-y-0.5 hover:border-white/40 hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                                >
                                    Demo ansehen
                                </a>
                            </div>

                            <dl class="mt-12 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-3xl bg-white/95 p-5 shadow-lg shadow-slate-950/20 ring-1 ring-white/30 backdrop-blur-sm">
                                    <dt class="text-sm text-slate-500">Offene Mietrechnungen</dt>
                                    <dd class="mt-2 text-2xl font-semibold text-slate-950">124</dd>
                                    <dd class="mt-1 text-sm text-slate-600">mit klarem Status und Zuordnung</dd>
                                </div>
                                <div class="rounded-3xl bg-white/95 p-5 shadow-lg shadow-slate-950/20 ring-1 ring-white/30 backdrop-blur-sm">
                                    <dt class="text-sm text-slate-500">Bankimporte</dt>
                                    <dd class="mt-2 text-2xl font-semibold text-slate-950">38</dd>
                                    <dd class="mt-1 text-sm text-slate-600">seit Montag automatisch erkannt</dd>
                                </div>
                                <div class="rounded-3xl bg-white/95 p-5 shadow-lg shadow-slate-950/20 ring-1 ring-white/30 backdrop-blur-sm">
                                    <dt class="text-sm text-slate-500">DATEV Export</dt>
                                    <dd class="mt-2 text-2xl font-semibold text-slate-950">bereit</dd>
                                    <dd class="mt-1 text-sm text-slate-600">fur den nachgelagerten Monatsabschluss</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-x-10 top-8 -z-10 h-48 rounded-full bg-indigo-200/40 blur-3xl"></div>

                            <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-950 shadow-2xl shadow-slate-300/70">
                                <div class="border-b border-white/10 px-6 py-5 text-white">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm text-slate-400">Monatsubersicht</p>
                                            <h2 class="mt-1 text-2xl font-semibold">Immobile Dashboard</h2>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300 ring-1 ring-emerald-400/20">
                                            System online
                                        </span>
                                    </div>
                                </div>

                                <div class="grid gap-5 p-6 text-white lg:grid-cols-[1.15fr_0.85fr]">
                                    <div class="space-y-5">
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <article class="rounded-3xl bg-white/5 p-4 ring-1 ring-white/10">
                                                <p class="text-sm text-slate-400">Offene Mietrechnungen</p>
                                                <p class="mt-2 text-3xl font-semibold">124</p>
                                                <p class="mt-2 text-sm text-emerald-300">82 % fristgerecht</p>
                                            </article>
                                            <article class="rounded-3xl bg-white/5 p-4 ring-1 ring-white/10">
                                                <p class="text-sm text-slate-400">Bankimport</p>
                                                <p class="mt-2 text-3xl font-semibold">38</p>
                                                <p class="mt-2 text-sm text-sky-300">12 Zahlungen erkannt</p>
                                            </article>
                                            <article class="rounded-3xl bg-white/5 p-4 ring-1 ring-white/10">
                                                <p class="text-sm text-slate-400">DATEV Export</p>
                                                <p class="mt-2 text-3xl font-semibold">1</p>
                                                <p class="mt-2 text-sm text-violet-300">bereit zur Ubergabe</p>
                                            </article>
                                        </div>

                                        <article class="rounded-[1.75rem] bg-white p-5 text-slate-900 shadow-lg shadow-slate-950/10">
                                            <div class="flex items-center justify-between gap-4">
                                                <div>
                                                    <p class="text-sm text-slate-500">Zahlungsstatus</p>
                                                    <h3 class="mt-1 text-lg font-semibold">Letzte Mietrechnungen</h3>
                                                </div>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Mai 2026</span>
                                            </div>

                                            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                                                <div class="grid grid-cols-[1.3fr_0.9fr_0.8fr] bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                                    <span>Mieter</span>
                                                    <span>Rechnung</span>
                                                    <span>Status</span>
                                                </div>
                                                <div class="divide-y divide-slate-200">
                                                    <div class="grid grid-cols-[1.3fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                                        <div>
                                                            <p class="font-medium text-slate-950">Anna Schubert</p>
                                                            <p class="text-slate-500">Objekt Allee 12</p>
                                                        </div>
                                                        <div class="font-medium text-slate-700">850,00 EUR</div>
                                                        <div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">bezahlt</span></div>
                                                    </div>
                                                    <div class="grid grid-cols-[1.3fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                                        <div>
                                                            <p class="font-medium text-slate-950">Markus Weber</p>
                                                            <p class="text-slate-500">Hofgarten 8</p>
                                                        </div>
                                                        <div class="font-medium text-slate-700">1.240,00 EUR</div>
                                                        <div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">offen</span></div>
                                                    </div>
                                                    <div class="grid grid-cols-[1.3fr_0.9fr_0.8fr] items-center px-4 py-3 text-sm">
                                                        <div>
                                                            <p class="font-medium text-slate-950">Sabine Keller</p>
                                                            <p class="text-slate-500">Rheinblick 3</p>
                                                        </div>
                                                        <div class="font-medium text-slate-700">970,00 EUR</div>
                                                        <div><span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">zugeordnet</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    </div>

                                    <div class="space-y-5">
                                        <article class="rounded-[1.75rem] bg-white/5 p-5 ring-1 ring-white/10">
                                            <div class="flex items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-sm text-slate-400">Bankimport</p>
                                                    <h3 class="mt-1 text-lg font-semibold">Zuordnung lauft</h3>
                                                </div>
                                                <span class="rounded-full bg-sky-400/10 px-3 py-1 text-xs font-semibold text-sky-300">12 erkannt</span>
                                            </div>
                                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                                                <div class="h-full w-3/4 rounded-full bg-gradient-to-r from-sky-400 to-indigo-400"></div>
                                            </div>
                                            <div class="mt-4 space-y-3 text-sm">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-slate-300">SEPA Sammelimport</span>
                                                    <span class="font-medium text-white">75 %</span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-slate-300">Prufbedarf</span>
                                                    <span class="font-medium text-amber-300">3 Buchungen</span>
                                                </div>
                                            </div>
                                        </article>

                                        <article class="rounded-[1.75rem] bg-white p-5 text-slate-900 shadow-lg shadow-slate-950/10">
                                            <p class="text-sm text-slate-500">DATEV Export</p>
                                            <h3 class="mt-1 text-lg font-semibold">Abschluss April 2026</h3>
                                            <div class="mt-4 space-y-3 text-sm">
                                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                                    <span>Belege vorbereitet</span>
                                                    <span class="font-semibold text-slate-950">218</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                                    <span>Mandanten gepruft</span>
                                                    <span class="font-semibold text-slate-950">4 / 4</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                                    <span>Status</span>
                                                    <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">exportbereit</span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    </div>
                </section>

                <section id="funktionen" class="py-20 sm:py-24">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Funktionen</p>
                            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                                Produktfunktionen fur den operativen Alltag von Hausverwaltungen.
                            </h2>
                            <p class="mt-5 text-lg leading-8 text-slate-600">
                                Jede Karte zeigt einen echten Anwendungsschwerpunkt der Plattform, statt abstrakter Platzhalter oder generischer Marketingflachen.
                            </p>
                        </div>

                        <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($featureCards as $feature)
                                <article class="group rounded-[1.75rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-1 hover:shadow-xl hover:ring-slate-300">
                                    <div class="flex items-start justify-between gap-4">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-slate-900 text-white shadow-lg shadow-indigo-500/20">
                                            @switch($feature['icon'])
                                                @case('document')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75H6.75A2.25 2.25 0 004.5 6v12a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 18V7.5l-3.75-3.75z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3.75V8.25h4.5" />
                                                    </svg>
                                                    @break
                                                @case('invoice')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h7.379a1.5 1.5 0 011.06.44l2.871 2.87a1.5 1.5 0 01.44 1.061V19.5A1.5 1.5 0 0117.75 21h-10.5a1.5 1.5 0 01-1.5-1.5v-14a1.5 1.5 0 011.5-1.5z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9.75h7.5m-7.5 3h7.5m-7.5 3h4.5" />
                                                    </svg>
                                                    @break
                                                @case('bank')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 4l9 6.5M4.5 9.75V18A1.5 1.5 0 006 19.5h12A1.5 1.5 0 0019.5 18V9.75M8.25 13.5v3m3-3v3m3-3v3" />
                                                    </svg>
                                                    @break
                                                @case('datev')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h10.5M9 12h10.5M9 17.25h10.5M4.5 6.75h.008v.008H4.5V6.75zm0 5.25h.008v.008H4.5V12zm0 5.25h.008v.008H4.5v-.008z" />
                                                    </svg>
                                                    @break
                                                @case('portal')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 1115 0" />
                                                    </svg>
                                                    @break
                                                @case('warning')
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a1.875 1.875 0 001.61 2.813h16.94A1.875 1.875 0 0021.98 18L13.51 3.86a1.875 1.875 0 00-3.22 0z" />
                                                    </svg>
                                                    @break
                                            @endswitch
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $feature['meta'] }}</span>
                                    </div>

                                    <h3 class="mt-6 text-xl font-semibold text-slate-950">{{ $feature['title'] }}</h3>
                                    <p class="mt-3 text-base leading-7 text-slate-600">{{ $feature['description'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="workflow" class="bg-white py-20 sm:py-24">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-3xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-600">Workflow</p>
                                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                                    Ein klarer Ablauf von Vertrag bis Buchhaltung.
                                </h2>
                                <p class="mt-5 text-lg leading-8 text-slate-600">
                                    Die Produktlogik ist auf wiederkehrende Aufgaben in der deutschen Hausverwaltung ausgerichtet: strukturiert, sichtbar und ohne leere Zwischenschritte.
                                </p>
                            </div>
                            <div class="rounded-2xl bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-700 ring-1 ring-indigo-100">
                                4 Schritte fur den Monatsablauf
                            </div>
                        </div>

                        <div class="mt-12 grid gap-6 lg:grid-cols-4">
                            @foreach ($workflowSteps as $step)
                                <article class="relative rounded-[1.75rem] bg-slate-50 p-6 ring-1 ring-slate-200">
                                    @if (! $loop->last)
                                        <span class="absolute left-full top-12 hidden h-px w-6 bg-slate-300 lg:block"></span>
                                    @endif
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white">
                                        {{ $step['number'] }}
                                    </div>
                                    <h3 class="mt-5 text-xl font-semibold text-slate-950">{{ $step['title'] }}</h3>
                                    <p class="mt-3 text-base leading-7 text-slate-600">{{ $step['description'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="mieterportal" class="py-20 sm:py-24">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="grid gap-10 rounded-[2rem] bg-slate-950 px-6 py-8 text-white shadow-2xl shadow-slate-300/70 sm:px-8 sm:py-10 lg:grid-cols-[0.95fr_1.05fr] lg:px-10">
                            <div class="max-w-xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-200">Mieterportal</p>
                                <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                                    Ein digitaler Zugang, der fur Mieter sofort verstandlich ist.
                                </h2>
                                <p class="mt-5 text-lg leading-8 text-slate-300">
                                    Dokumente, Rechnungen und Nachrichten werden gebundelt bereitgestellt. Das reduziert Ruckfragen und wirkt gegenuber Mietern professionell und modern.
                                </p>

                                <ul class="mt-8 space-y-4 text-sm leading-6 text-slate-300">
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                        Offene Dokumente und Vertrage an einem Ort statt verteilt per E-Mail.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-sky-400"></span>
                                        Letzte Mietrechnung sofort sichtbar, inklusive Bereitstellungsstatus.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-violet-400"></span>
                                        Nachrichten vom Vermieter nachvollziehbar und mit Zeitbezug dokumentiert.
                                    </li>
                                </ul>

                                @if (Route::has('portal.login'))
                                    <div class="mt-8">
                                        <a
                                            href="{{ route('portal.login') }}"
                                            class="inline-flex min-h-12 items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition duration-200 hover:-translate-y-0.5 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                        >
                                            Zum Mieterportal
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-[1.75rem] bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
                                <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
                                    <div>
                                        <p class="text-sm text-slate-500">Portalansicht</p>
                                        <h3 class="mt-1 text-xl font-semibold">Willkommen, Familie Weber</h3>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">aktiv</span>
                                </div>

                                <div class="mt-5 grid gap-4 lg:grid-cols-[1.05fr_0.95fr]">
                                    <div class="space-y-4">
                                        <article class="rounded-3xl bg-slate-50 p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-sm text-slate-500">Offene Dokumente</p>
                                                    <p class="mt-1 font-semibold text-slate-950">Mietvertrag 2026.pdf</p>
                                                </div>
                                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">neu</span>
                                            </div>
                                            <p class="mt-3 text-sm text-slate-600">Digital freigegeben und jederzeit herunterladbar.</p>
                                        </article>

                                        <article class="rounded-3xl bg-slate-50 p-4">
                                            <p class="text-sm text-slate-500">Letzte Rechnung</p>
                                            <div class="mt-2 flex items-center justify-between gap-3">
                                                <p class="text-lg font-semibold text-slate-950">Mietrechnung Mai 2026</p>
                                                <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">bereitgestellt</span>
                                            </div>
                                            <p class="mt-3 text-sm text-slate-600">Betrag: 1.240,00 EUR</p>
                                        </article>
                                    </div>

                                    <article class="rounded-3xl bg-slate-950 p-5 text-white">
                                        <p class="text-sm text-slate-400">Nachricht vom Vermieter</p>
                                        <h4 class="mt-2 text-lg font-semibold">Nebenkostenunterlagen aktualisiert</h4>
                                        <p class="mt-4 text-sm leading-7 text-slate-300">
                                            Guten Tag Frau Weber, die aktuellen Unterlagen fur den Abrechnungszeitraum stehen nun im Portal bereit. Bei Ruckfragen konnen Sie direkt antworten.
                                        </p>
                                        <div class="mt-5 flex items-center justify-between border-t border-white/10 pt-4 text-xs text-slate-400">
                                            <span>Heute, 09:42 Uhr</span>
                                            <span class="rounded-full bg-white/10 px-2.5 py-1 font-semibold text-slate-200">zugestellt</span>
                                        </div>
                                    </article>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="preise" class="pb-20 pt-8 sm:pb-24">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-900 px-6 py-10 text-white shadow-2xl shadow-slate-300/70 sm:px-8 lg:px-10">
                            <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                                <div class="max-w-3xl">
                                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-200">Preise & Einstieg</p>
                                    <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                                        Starten Sie mit Ihrer digitalen Hausverwaltung
                                    </h2>
                                    <p class="mt-5 text-lg leading-8 text-slate-300">
                                        Statt fixe Marketingpreise zu erfinden, fuhrt die Seite klar in den Einstieg: registrieren, testen und die Plattform fur den eigenen Verwaltungsprozess bewerten.
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="inline-flex min-h-12 items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:-translate-y-0.5 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                        >
                                            Kostenlos registrieren
                                        </a>
                                    @endif
                                    @if (Route::has('login'))
                                        <a
                                            href="{{ route('login') }}"
                                            class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                        >
                                            Anmelden
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-md">
                            <p class="text-lg font-semibold text-slate-950">Immobile</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Hausverwaltung einfach digital. Fur Mietvertrage, Mietabrechnung, Bankabgleich, Mahnwesen und Mieterportal in einer Plattform.
                            </p>
                        </div>

                        <div class="grid gap-8 sm:grid-cols-2">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-950">Navigation</h3>
                                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                                    @foreach ($footerLinks as $link)
                                        <li><a href="{{ $link['href'] }}" class="transition hover:text-slate-950">{{ $link['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-slate-950">Zugange</h3>
                                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                                    @if (Route::has('login'))
                                        <li><a href="{{ route('login') }}" class="transition hover:text-slate-950">Anmelden</a></li>
                                    @endif
                                    @if (Route::has('register'))
                                        <li><a href="{{ route('register') }}" class="transition hover:text-slate-950">Kostenlos testen</a></li>
                                    @endif
                                    @if (Route::has('portal.login'))
                                        <li><a href="{{ route('portal.login') }}" class="transition hover:text-slate-950">Mieterportal</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
