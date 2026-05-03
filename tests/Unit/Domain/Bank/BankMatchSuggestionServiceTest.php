<?php

namespace Tests\Unit\Domain\Bank;

use App\Domain\Bank\BankMatchSuggestionService;
use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankMatchSuggestionServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_ranks_exact_open_amount_and_tenant_name_matches(): void
    {
        $user = User::factory()->create();
        $mandantId = $user->mandant_id;
        $mieter = Mieter::factory()->create([
            'mandant_id' => $mandantId,
            'name' => 'Anna Schmidt',
        ]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $mandantId,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 500_00,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $import = BankImport::query()->create([
            'mandant_id' => $mandantId,
            'original_filename' => 't.csv',
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
        ]);

        $tx = BankTransaction::query()->create([
            'mandant_id' => $mandantId,
            'bank_import_id' => $import->id,
            'buchungsdatum' => now()->toDateString(),
            'betrag_cent' => 500_00,
            'gegenpartei' => null,
            'verwendungszweck' => 'Miete Anna Schmidt Mai',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        $openInvoices = Rechnung::query()->whereKey($rechnung->id)->with('mieter')->get();
        $transactions = collect([$tx]);

        $service = new BankMatchSuggestionService;
        $map = $service->forTransactions($transactions, $openInvoices);

        $this->assertArrayHasKey($tx->id, $map);
        $first = $map[$tx->id][0];
        $this->assertSame($rechnung->id, $first['rechnung']->id);
        $this->assertSame(150, $first['score']);
        $this->assertContains('exact_open_amount', $first['reasons']);
        $this->assertContains('tenant_in_text', $first['reasons']);
    }

    #[Test]
    public function it_never_emits_suggestions_for_fully_allocated_transactions(): void
    {
        $mandant = Mandant::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 100,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $import = BankImport::query()->create([
            'mandant_id' => $mandant->id,
            'original_filename' => 't.csv',
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
        ]);

        $tx = BankTransaction::query()->create([
            'mandant_id' => $mandant->id,
            'bank_import_id' => $import->id,
            'buchungsdatum' => now()->toDateString(),
            'betrag_cent' => 0,
            'gegenpartei' => null,
            'verwendungszweck' => null,
            'raw_row' => null,
            'status' => BankTransaction::STATUS_ZUGEORDNET,
        ]);

        $service = new BankMatchSuggestionService;
        $map = $service->forTransactions(collect([$tx]), Rechnung::query()->whereKey($rechnung->id)->with('mieter')->get());

        $this->assertSame([], $map[$tx->id]);
    }
}
