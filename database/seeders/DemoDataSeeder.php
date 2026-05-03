<?php

namespace Database\Seeders;

use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fills the DB with a coherent demo portfolio for local/staging use.
 * Login: demo@example.com / password
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->where('email', 'demo@example.com')->exists()) {
            $this->command?->warn('Demo seed skipped: user demo@example.com already exists.');

            return;
        }

        $mandant = Mandant::query()->create([
            'name' => 'Demo Hausverwaltung GmbH',
        ]);

        User::query()->create([
            'name' => 'Demo Admin',
            'email' => 'demo@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'mandant_id' => $mandant->id,
            'role' => User::ROLE_OWNER,
        ]);

        $objektBerlin = Objekt::query()->create([
            'mandant_id' => $mandant->id,
            'name' => 'Berliner Straße 12',
        ]);

        $objektMuenchen = Objekt::query()->create([
            'mandant_id' => $mandant->id,
            'name' => 'München Sonnenallee 3',
        ]);

        $e1 = Einheit::query()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objektBerlin->id,
            'name' => 'WE 1 OG links',
        ]);

        $e2 = Einheit::query()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objektBerlin->id,
            'name' => 'WE 2 OG rechts',
        ]);

        $e3 = Einheit::query()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objektMuenchen->id,
            'name' => 'WE 5 DG',
        ]);

        $mSchmidt = Mieter::query()->create([
            'mandant_id' => $mandant->id,
            'name' => 'Anna Schmidt',
            'email' => 'anna.schmidt@example.org',
        ]);

        $mWeber = Mieter::query()->create([
            'mandant_id' => $mandant->id,
            'name' => 'Thomas Weber',
            'email' => 'thomas.weber@example.org',
        ]);

        $mKlein = Mieter::query()->create([
            'mandant_id' => $mandant->id,
            'name' => 'Lisa Klein',
            'email' => null,
        ]);

        Mietvertrag::query()->create([
            'mandant_id' => $mandant->id,
            'einheit_id' => $e1->id,
            'mieter_id' => $mSchmidt->id,
            'starts_on' => Carbon::parse('2024-01-01')->toDateString(),
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 95_000,
            'nebenkosten_vorauszahlung_cent' => 25_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => 3,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mWeber->id,
            'einheit_id' => $e2->id,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 850_50,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => Carbon::parse('+10 days')->toDateString(),
            'bezahlt_am' => null,
        ]);

        Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mKlein->id,
            'einheit_id' => $e3->id,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 450_00,
            'source_data' => null,
            'status' => Rechnung::STATUS_BEZAHLT,
            'faellig_am' => Carbon::parse('-30 days')->toDateString(),
            'bezahlt_am' => now()->subDays(20),
        ]);

        $import = BankImport::query()->create([
            'mandant_id' => $mandant->id,
            'original_filename' => 'demo-umsatz.csv',
            'row_count' => 2,
            'status' => BankImport::STATUS_COMPLETED,
        ]);

        BankTransaction::query()->create([
            'mandant_id' => $mandant->id,
            'bank_import_id' => $import->id,
            'buchungsdatum' => Carbon::parse('2026-05-02'),
            'betrag_cent' => 120_000,
            'gegenpartei' => 'Anna Schmidt',
            'verwendungszweck' => '[DEMO] Miete WE1 Anna Schmidt — WOW Demo',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        BankTransaction::query()->create([
            'mandant_id' => $mandant->id,
            'bank_import_id' => $import->id,
            'buchungsdatum' => Carbon::parse('2026-05-03'),
            'betrag_cent' => 50_000,
            'gegenpartei' => 'Unbekannt',
            'verwendungszweck' => 'Diverse',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        $this->command?->info('Demo data created. Login: demo@example.com / password');
    }
}
