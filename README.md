# Immobile

[![CI](https://github.com/PayamAbdolmohammadi/immobile/actions/workflows/ci.yml/badge.svg)](https://github.com/PayamAbdolmohammadi/immobile/actions/workflows/ci.yml)
[![Website](https://img.shields.io/website?url=https%3A%2F%2Fimmobilen.payamdev.de&label=production&up_message=online&down_message=offline)](https://immobilen.payamdev.de)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)

Production-ready SaaS platform for managing **German-style rental & property operations** — tenants, leases, automated billing, operating cost settlements, bank matching, and tenant-facing workflows.

Built with a modern full-stack architecture:

- **Laravel 13** (PHP 8.3)
- **MySQL / SQLite**
- **Vite + Tailwind CSS + Alpine.js**
- **Docker + Nginx**
- **AWS EC2 + Let’s Encrypt SSL**

Default locale and UI language: **German** (`de_DE`).

## Product vision

> „Die Miete läuft automatisch.“

Immobile turns rental contracts into an automated cashflow engine: recurring rent runs, tracking, reminders, and clear landlord/tenant workflows.

## Core features

| Area | Description |
|------|-------------|
| **Portfolio management** | Tenants (`Mieter`), properties (`Objekte`), units (`Einheiten`), rental agreements (`Mietverträge`) including PDF contract links + public acceptance (`/contracts/{token}`). |
| **Billing engine** | Automated monthly rent generation, invoices, reminders (`Mahnung`), cancellations (`Storno`). |
| **Operating costs (NK)** | Full Nebenkostenabrechnung workflow with PDF export. |
| **Bank integration** | CSV import + transaction matching to invoices and tenants. |
| **Accounting export** | Simple CSV + DATEV-oriented export. |
| **Tenant portal** | Dedicated `/portal` area with separate guard (`mieter`), dashboard, and document access. |
| **Demo flow** | Guided landlord demo under `/demo-flow`. |
| **Onboarding** | Mandatory mandant setup before the full system unlocks (`/mandant/einrichten`). |

## Authentication

- **Staff**: Laravel Breeze
- **Email verification** required for the main app
- **Tenant portal**: separate guard (`mieter`)

PDF generation uses **barryvdh/laravel-dompdf**.

## Requirements

- **PHP** ^8.3 with usual Laravel extensions (mbstring, openssl, pdo, etc.)
- **Composer** 2.x  
- **Node.js** and **npm** (for Vite / frontend build)
- **MySQL** (optional) or **SQLite** (default)

Default database in `.env.example` is **SQLite**; you can switch to MySQL/PostgreSQL via `DB_*` variables.

## Quick start

```bash
git clone git@github.com:PayamAbdolmohammadi/immobilen.git
cd immobilen

# One-shot: install deps, .env, key, migrate, npm install + build
composer run setup
```

Then start the local dev stack (HTTP server + Vite):

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

## Docker (production-like)

Start the production stack with Docker + Nginx:

```bash
docker compose up -d --build
```

Local URL (default): **http://127.0.0.1:8080**

Production URL: `https://immobilen.payamdev.de`

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

## Important routes

| Route | Purpose |
|------|---------|
| `/` | Landing page |
| `/dashboard` | Main application |
| `/portal/login` | Tenant portal login |
| `/demo-flow` | Guided product demo |
| `/contracts/{token}` | Public contract page (48-char hex token) |

## Tests

```bash
composer test
```

Runs `php artisan test` after clearing config cache.

## Project structure (high level)

- `app/Http/Controllers` — HTTP layer (resources, portal, bank, exports, demo flow).
- `app/Domain` — Billing and domain services where extracted.
- `resources/views` — Blade UI (German strings).
- `routes/web.php` — Primary web routes.
- `database/migrations`, `database/seeders` — Schema and demo seeds.

## Architecture (production)

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

## Deployment

- AWS EC2
- Docker containers
- Nginx reverse proxy
- Let’s Encrypt SSL
- Custom domain (IONOS DNS)

## Configuration notes

- **Timezone:** `Europe/Berlin` in `.env.example`.
- **Session / cache / queue:** defaults point at **database** drivers; ensure migrations have been run.
- **Mail:** `log` driver in `.env.example` — suitable for local dev only.

## Documentation

- [docs/PHASE_2_TECHNIK_CHECKLISTE.md](docs/PHASE_2_TECHNIK_CHECKLISTE.md)

## License

The Laravel framework and this application’s original Laravel-scaffolded portions are open source under the [MIT license](https://opensource.org/licenses/MIT). Third-party packages retain their respective licenses.
