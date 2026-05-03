<?php

namespace Tests\Feature;

use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class Phase5Test extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_download_accounting_csv(): void
    {
        $user = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id]);
        Rechnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 10_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('export.accounting.simple-csv'), [
                'year' => (int) now()->format('Y'),
            ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('100,00', $response->streamedContent());
    }

    public function test_staff_cannot_download_accounting_csv(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user)
            ->get(route('export.accounting.index'))
            ->assertForbidden();
    }

    public function test_staff_cannot_delete_tenant(): void
    {
        $user = User::factory()->staff()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id]);

        $this->actingAs($user)
            ->delete(route('mieter.destroy', $mieter))
            ->assertForbidden();
    }

    public function test_failed_bank_import_is_logged(): void
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent('bad.csv', '');

        $this->actingAs($user)
            ->post(route('bank.import'), ['file' => $file])
            ->assertRedirect(route('bank.matching'));

        $this->assertDatabaseHas('bank_imports', [
            'mandant_id' => $user->mandant_id,
            'status' => \App\Models\BankImport::STATUS_FAILED,
        ]);
    }
}
