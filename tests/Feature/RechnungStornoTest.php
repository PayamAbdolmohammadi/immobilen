<?php

namespace Tests\Feature;

use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use App\Models\Zahlungszuordnung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RechnungStornoTest extends TestCase
{
    use RefreshDatabase;

    private function rentInvoiceFixture(): array
    {
        $mandant = Mandant::factory()->create();
        $user = User::factory()->create(['mandant_id' => $mandant->id]);
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);
        $lease = Mietvertrag::query()->create([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mieter->id,
            'einheit_id' => $einheit->id,
            'mietvertrag_id' => $lease->id,
            'typ' => Rechnung::TYP_RENT,
            'billing_period' => '2026-05',
            'betrag_cent' => 100_000,
            'source_data' => ['total_cent' => 100_000],
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => '2026-05-31',
            'bezahlt_am' => null,
        ]);

        return compact('user', 'rechnung');
    }

    public function test_owner_can_storno_open_rent_invoice_without_allocations(): void
    {
        ['user' => $user, 'rechnung' => $rechnung] = $this->rentInvoiceFixture();

        $this->actingAs($user)
            ->post(route('rechnungen.storno', $rechnung), ['confirm' => '1'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'rechnung-storniert');

        $this->assertSame(Rechnung::STATUS_STORNIERT, $rechnung->fresh()->status);
        $this->assertNotNull($rechnung->fresh()->storniert_am);
    }

    public function test_staff_cannot_storno(): void
    {
        ['user' => $owner, 'rechnung' => $rechnung] = $this->rentInvoiceFixture();
        $staff = User::factory()->staff()->create(['mandant_id' => $owner->mandant_id]);

        $this->actingAs($staff)
            ->post(route('rechnungen.storno', $rechnung), ['confirm' => '1'])
            ->assertForbidden();
    }

    public function test_cannot_storno_when_bank_allocated(): void
    {
        ['user' => $user, 'rechnung' => $rechnung] = $this->rentInvoiceFixture();

        $bankImport = BankImport::query()->create([
            'mandant_id' => $user->mandant_id,
            'original_filename' => 'x.csv',
            'csv_profile' => null,
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
            'error_message' => null,
        ]);

        $txn = BankTransaction::query()->create([
            'mandant_id' => $user->mandant_id,
            'bank_import_id' => $bankImport->id,
            'buchungsdatum' => now()->startOfDay(),
            'betrag_cent' => 100_000,
            'gegenpartei' => null,
            'verwendungszweck' => null,
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        Zahlungszuordnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'bank_transaction_id' => $txn->id,
            'rechnung_id' => $rechnung->id,
            'betrag_cent' => 50_000,
        ]);

        $this->actingAs($user)
            ->post(route('rechnungen.storno', $rechnung), ['confirm' => '1'])
            ->assertForbidden();
    }
}
