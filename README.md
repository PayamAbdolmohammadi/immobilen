این README رو برات حرفه‌ای‌تر، تمیزتر و مناسب CV/Recruiter/GitHub بازنویسی کردم — همون محتوا ولی قوی‌تر و واضح‌تر 👇
🏢 Immobile







Production-ready SaaS platform for managing German-style rental and property operations — including tenants, leases, billing, operating cost settlements, bank matching, and tenant-facing workflows.
Built with a modern full-stack architecture using:


Laravel 13 (PHP 8.3)


MySQL / SQLite


Vite + Tailwind CSS + Alpine.js


Docker + Nginx


AWS EC2 + Let’s Encrypt SSL


👉 Default locale and UI language: German (de_DE)

🚀 Core Features
AreaDescriptionPortfolio ManagementManage tenants (Mieter), properties (Objekte), units (Einheiten), and rental agreements (Mietverträge)Billing EngineAutomated monthly rent generation, invoices, reminders (Mahnung), cancellations (Storno)Operating CostsFull Nebenkostenabrechnung (NK) workflow with PDF exportBank IntegrationCSV import + transaction matching to invoices and tenantsAccounting ExportSimple CSV + DATEV-ready exportTenant PortalDedicated /portal area with login, dashboard, and document accessDemo FlowGuided landlord demo under /demo-flowOnboardingMandatory mandant setup before accessing the full system

🧠 Product Vision

„Die Miete läuft automatisch.“

Immobile transforms rental contracts into an automated billing and property workflow platform for landlords and property managers.

🔐 Authentication


Staff authentication via Laravel Breeze


Email verification required


Separate tenant portal guard (mieter)



📦 Tech Stack
LayerTechnologyBackendLaravel 13 (PHP 8.3)FrontendBlade, Tailwind CSS, Alpine.jsDatabaseMySQL / SQLitePDFbarryvdh/laravel-dompdfDevOpsDocker, NginxHostingAWS EC2SSLLet’s Encrypt

⚙️ Requirements


PHP ^8.3


Composer 2.x


Node.js + npm


MySQL / SQLite



⚡ Quick Start
git clone git@github.com:PayamAbdolmohammadi/immobilen.gitcd immobilencomposer run setupcomposer run dev
Open:
http://127.0.0.1:8000

🛠 Manual Setup
composer installcp .env.example .envphp artisan key:generatetouch database/database.sqlitephp artisan migratenpm installnpm run devphp artisan serve

🐳 Docker (Production)
Start the production stack:
docker compose up -d --build
Production URL:
https://immobilen.payamdev.de

🧪 Demo Data
Seed demo data:
php artisan db:seed
Demo credentials:
Email: demo@example.comPassword: password

🔗 Important Routes
RoutePurpose/Landing page/dashboardMain application/portal/loginTenant portal/demo-flowGuided product demo/contracts/{token}Public contract page

🧪 Testing
composer test
Runs Laravel tests after clearing config cache.

📁 Project Structure
app/├── Http/Controllers├── Domain/resources/views/routes/web.phpdatabase/docs/

🏗 Architecture (Production)


☁️ Deployment
The application is deployed in a production environment using:


AWS EC2


Docker containers


Nginx reverse proxy


Let’s Encrypt SSL


Custom domain via IONOS DNS



⚙️ Configuration Notes


Timezone: Europe/Berlin


Queue / Cache: database driver


Mail driver: log (development only)



📚 Documentation


docs/PHASE_2_TECHNIK_CHECKLISTE.md



🧾 License
MIT License (Laravel + project code).
Third-party packages retain their respective licenses.

👨‍💻 Author
Payam Abdolmohammadi
Full-Stack Software Engineer
Laravel • SaaS • APIs • AWS • Docker
