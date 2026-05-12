<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Einheit;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Models\NkAbrechnung;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RouteBindingIsolationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_cannot_access_other_mandants_core_resources_via_route_model_binding(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $mieterB = Mieter::factory()->create(['mandant_id' => $userB->mandant_id]);
        $objektB = Objekt::factory()->create(['mandant_id' => $userB->mandant_id]);
        $einheitB = Einheit::factory()->create([
            'mandant_id' => $userB->mandant_id,
            'objekt_id' => $objektB->id,
        ]);
        $leaseB = Mietvertrag::factory()->create([
            'mandant_id' => $userB->mandant_id,
            'einheit_id' => $einheitB->id,
            'mieter_id' => $mieterB->id,
        ]);
        $nkB = NkAbrechnung::query()->create([
            'mandant_id' => $userB->mandant_id,
            'objekt_id' => $objektB->id,
            'jahr' => 2026,
            'verteilungs_art' => NkAbrechnung::VERTEILUNG_GLEICH_PRO_EINHEIT,
            'status' => NkAbrechnung::STATUS_DRAFT,
        ]);

        $this->actingAs($userA)->get(route('mieter.show', $mieterB))->assertNotFound();
        $this->actingAs($userA)->get(route('objekte.show', $objektB))->assertNotFound();
        $this->actingAs($userA)->get(route('einheiten.show', $einheitB))->assertNotFound();
        $this->actingAs($userA)->get(route('mietvertraege.show', $leaseB))->assertNotFound();
        $this->actingAs($userA)->get(route('nk-abrechnungen.show', $nkB))->assertNotFound();
        $this->actingAs($userA)->get(route('nk-abrechnungen.pdf', $nkB))->assertNotFound();
    }

    #[Test]
    public function user_cannot_access_other_mandants_invoices_via_route_model_binding(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $invoiceB = Rechnung::factory()->create([
            'mandant_id' => $userB->mandant_id,
            'typ' => Rechnung::TYP_MANUAL,
        ]);

        $this->actingAs($userA)
            ->post(route('rechnungen.mahnung', $invoiceB))
            ->assertNotFound();

        $this->actingAs($userA)
            ->post(route('rechnungen.storno', $invoiceB), ['confirm' => '1'])
            ->assertNotFound();

        $this->actingAs($userA)
            ->get(route('manual-rechnungen.edit', ['manual_rechnung' => $invoiceB->id]))
            ->assertNotFound();
    }

    #[Test]
    public function user_cannot_allocate_bank_transaction_from_other_mandant_via_route_model_binding(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $importB = BankImport::query()->create([
            'mandant_id' => $userB->mandant_id,
            'original_filename' => 'x.csv',
            'csv_profile' => null,
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
            'error_message' => null,
        ]);

        $txB = BankTransaction::query()->create([
            'mandant_id' => $userB->mandant_id,
            'bank_import_id' => $importB->id,
            'buchungsdatum' => now()->startOfDay(),
            'betrag_cent' => 100_000,
            'gegenpartei' => null,
            'verwendungszweck' => 'Cross-tenant test',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        $this->actingAs($userA)
            ->post(route('bank.allocate', ['bank_transaction' => $txB->id]), [
                'rechnung_id' => 1,
                'betrag' => '1.00',
            ])
            ->assertNotFound();
    }
}

