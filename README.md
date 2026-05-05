# Immobile

[![CI](https://github.com/PayamAbdolmohammadi/immobile/actions/workflows/ci.yml/badge.svg)](https://github.com/PayamAbdolmohammadi/immobile/actions/workflows/ci.yml)
[![Website](https://img.shields.io/website?url=https%3A%2F%2Fimmobilen.payamdev.de&label=production&up_message=online&down_message=offline)](https://immobilen.payamdev.de)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)

Web application for **German-style rental and property operations** (objects, units, leases, operating cost settlements, bank matching, invoices, and tenant-facing flows). Built with **Laravel 13**, **PHP 8.3**, **Vite**, **Tailwind CSS**, and **Alpine.js**. Default locale and copy are **German** (`de` / `de_DE`).

## Features

| Area | What you get |
|------|----------------|
| **Portfolio** | Tenants (`Mieter`), buildings (`Objekte`), units (`Einheiten`), rental agreements (`Mietvertraege`) with PDF contract links and public acceptance URLs (`/contracts/{token}`). |
| **Billing** | Monthly rent runs, manual invoices, reminders (`Mahnung`), storno, operating-cost settlements (`NK-Abrechnungen`) with PDF export. |
| **Bank** | CSV import, open transactions, allocation / matching to tenants and invoices. |
| **Exports** | Accounting exports (simple CSV and DATEV-oriented CSV) for owners (`/export/accounting`). |
| **Tenant portal** | Separate area under `/portal` (guard `mieter`) for tenant login and dashboard. |
| **WOW demo** | Guided demo for **property owners** at **`/demo-flow`**: auto demo steps, sample bank CSV, rent generation helpers (requires owner role and completed mandant setup). |
| **Onboarding** | After registration, users complete **mandant** setup (`/mandant/einrichten`) before the main app routes unlock. |

Authentication for staff uses **Laravel Breeze** (email verification required for the main app). PDFs use **barryvdh/laravel-dompdf**.

## Requirements

- **PHP** ^8.3 with usual Laravel extensions (mbstring, openssl, pdo, etc.)
- **Composer** 2.x  
- **Node.js** and **npm** (for Vite / frontend build)

Default database in `.env.example` is **SQLite**; you can switch to MySQL/PostgreSQL via `DB_*` variables.

## Quick start

```bash
git clone git@github.com:PayamAbdolmohammadi/immobilen.git
cd immobilen

# One-shot: install deps, .env, key, migrate, npm install + build
composer run setup
```

Then start the stack for local development (HTTP server, queue worker, logs, Vite):

```bash
composer run dev
```

Open **http://127.0.0.1:8000** (or the URL shown by `artisan serve`).

### Manual setup (alternative)

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # if using SQLite
php artisan migrate
npm install
npm run dev                      # terminal 1 — Vite
php artisan serve                # terminal 2 — app
```

If you use queues or scheduled jobs in production, run a **queue worker** (`php artisan queue:work`) and configure a **scheduler** cron for `php artisan schedule:run`.

## Demo data (local / staging)

Seed the database (includes demo mandant, bank lines, leases, etc.):

```bash
php artisan db:seed
```

**Staff (Breeze) demo login** (from `DemoDataSeeder`):

- Email: `demo@example.com`  
- Password: `password`

Use only on trusted environments; change or remove demo users in production.

## Useful URLs

| URL | Purpose |
|-----|---------|
| `/` | Product landing / welcome |
| `/register`, `/login` | Staff account (Breeze) |
| `/mandant/einrichten` | First-time mandant setup (authenticated, before full app) |
| `/dashboard` | Main app home (after email verified + mandant) |
| `/portal/login` | Tenant portal login |
| `/demo-flow` | WOW demo checklist (owner-only) |
| `/contracts/{token}` | Public contract view (48-char hex token) |

## Tests

```bash
composer test
```

Runs `php artisan test` after clearing config cache.

## Project layout (high level)

- `app/Http/Controllers` — HTTP layer (resources, portal, bank, exports, demo flow).
- `app/Domain` — Billing and domain services where extracted.
- `resources/views` — Blade UI (German strings).
- `routes/web.php` — Primary web routes.
- `database/migrations`, `database/seeders` — Schema and demo seeds.

## Architecture (production / Docker)

```mermaid
flowchart TB
  U[Users (Landlords & Tenants)] -->|HTTPS| N[Nginx (Reverse Proxy)]
  N -->|FastCGI| A[Laravel App (PHP-FPM)]

  A --> DB[(MySQL)]
  A --> R[(Redis)]

  Q[Queue Worker<br/>php artisan queue:work] --> R
  Q --> DB
  Q --> A

  S[Scheduler<br/>php artisan schedule:run] --> A

  A --- P[/Tenant portal<br/>/portal (guard: mieter)/]
  A --- D[/Main app<br/>/dashboard (Breeze + email verify)/]
  A --- DEMO[/Demo flow<br/>/demo-flow/]
  A --- PUB[/Public contract<br/>/contracts/{token}/]
```

## Documentation in this repo

- [docs/PHASE_2_TECHNIK_CHECKLISTE.md](docs/PHASE_2_TECHNIK_CHECKLISTE.md) — technical checklist (phase 2).

## Configuration notes

- **Timezone:** `Europe/Berlin` in `.env.example`.
- **Session / cache / queue:** defaults point at **database** drivers; ensure migrations have been run.
- **Mail:** `log` driver in `.env.example` — suitable for local dev only.

## License

The Laravel framework and this application’s original Laravel-scaffolded portions are open source under the [MIT license](https://opensource.org/licenses/MIT). Third-party packages retain their respective licenses.
