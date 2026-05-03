<?php

namespace Tests\Feature\Bank;

use App\Models\BankTransaction;
use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
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
}
