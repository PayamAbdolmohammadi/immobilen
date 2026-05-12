<?php

namespace Tests\Feature\Bank;

use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use App\Models\Zahlungszuordnung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BankCsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_creates_transactions(): void
    {
        $user = User::factory()->create();

        $csv = "Buchungstag;Betrag;Verwendungszweck\n02.05.2026;1500,00;Miete Mai\n";

        $file = UploadedFile::fake()->createWithContent('umsatz.csv', $csv);

        $this->actingAs($user)
            ->post(route('bank.import'), ['file' => $file])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('bank.matching'))
            ->assertSessionHas('status', 'bank-import-queued');

        $this->assertSame(1, BankTransaction::query()->where('mandant_id', $user->mandant_id)->count());
        $tx = BankTransaction::query()->first();
        $this->assertSame(150_000, (int) $tx->betrag_cent);
        $this->assertSame('Miete Mai', $tx->verwendungszweck);
    }

    public function test_allocate_payment_updates_invoice_and_transaction(): void
    {
        $user = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 150_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $csv = "Buchungstag;Betrag;Verwendungszweck\n02.05.2026;1500,00;Miete\n";

        $this->actingAs($user)
            ->post(route('bank.import'), [
                'file' => UploadedFile::fake()->createWithContent('x.csv', $csv),
            ])
            ->assertRedirect(route('bank.matching'))
            ->assertSessionHas('status', 'bank-import-queued');

        $tx = BankTransaction::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('bank.allocate', $tx), [
                'rechnung_id' => $rechnung->id,
                'betrag' => '1500.00',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('bank.matching'));

        $rechnung->refresh();
        $tx->refresh();

        $this->assertSame(Rechnung::STATUS_BEZAHLT, $rechnung->status);
        $this->assertNotNull($rechnung->bezahlt_am);
        $this->assertSame(BankTransaction::STATUS_ZUGEORDNET, $tx->status);
    }

    public function test_cross_tenant_allocation_is_rejected_and_creates_no_allocation(): void
    {
        $userA = User::factory()->create();

        $mandantB = Mandant::factory()->create();
        $userB = User::factory()->create(['mandant_id' => $mandantB->id]);
        $mieterB = Mieter::factory()->create(['mandant_id' => $mandantB->id]);

        $invoiceB = Rechnung::query()->create([
            'mandant_id' => $userB->mandant_id,
            'mieter_id' => $mieterB->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 150_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $importA = BankImport::query()->create([
            'mandant_id' => $userA->mandant_id,
            'original_filename' => 'a.csv',
            'csv_profile' => null,
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
            'error_message' => null,
        ]);

        $txA = BankTransaction::query()->create([
            'mandant_id' => $userA->mandant_id,
            'bank_import_id' => $importA->id,
            'buchungsdatum' => now()->startOfDay(),
            'betrag_cent' => 150_000,
            'gegenpartei' => null,
            'verwendungszweck' => 'Cross tenant allocation test',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        $this->assertSame(0, Zahlungszuordnung::query()->count());

        $this->actingAs($userA)
            ->postJson(route('bank.allocate', $txA), [
                'rechnung_id' => $invoiceB->id,
                'betrag' => '1500.00',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rechnung_id']);

        $this->assertSame(0, Zahlungszuordnung::query()->count());
        $this->assertSame(Rechnung::STATUS_OFFEN, $invoiceB->fresh()->status);
        $this->assertSame(BankTransaction::STATUS_OFFEN, $txA->fresh()->status);
    }
}
