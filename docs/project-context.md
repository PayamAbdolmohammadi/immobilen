# Project Context

This is a Laravel SaaS project for German property management.

Target users:
- Hausverwaltungen
- Vermieter
- small property management companies

Main modules:
- Objekte / properties
- Einheiten / units
- Mieter / tenants
- Mietverträge / lease contracts
- Mietrechnungen / rent invoices
- Mahnwesen / dunning
- Nebenkostenabrechnung / operating cost settlement
- Bank import and payment matching
- DATEV-ready export
- Mieterportal / tenant portal

Technical stack:
- Laravel
- PHP
- MySQL
- Docker
- AWS deployment
- PHPUnit
- Tailwind / Blade where applicable

Important architecture:
- Multi-tenant isolation with mandant_id
- Service classes for business logic
- Jobs for background processing
- Policies and middleware for access control
- Tenant portal separated from admin area
