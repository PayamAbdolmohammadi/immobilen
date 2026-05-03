<?php

namespace Tests\Feature;

use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_accounting_export_page(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user)
            ->get(route('export.accounting.index'))
            ->assertForbidden();
    }

    public function test_owner_can_access_accounting_export_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('export.accounting.index'))
            ->assertOk()
            ->assertSee(__('Accounting export'), false);
    }

    public function test_simple_export_contains_only_invoices_of_current_mandant(): void
    {
        $owner = User::factory()->create();
        $mieterOwn = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $invOwn = $this->makeInvoice($owner->mandant_id, $mieterOwn->id, [
            'betrag_cent' => 11_100,
            'status' => Rechnung::STATUS_OFFEN,
        ]);

        $otherMandant = Mandant::factory()->create();
        $mieterOther = Mieter::factory()->create(['mandant_id' => $otherMandant->id]);
        $invOther = $this->makeInvoice($otherMandant->id, $mieterOther->id, [
            'betrag_cent' => 22_200,
            'status' => Rechnung::STATUS_OFFEN,
        ]);

        $year = (int) $invOwn->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), [
                'year' => $year,
            ])
            ->assertOk()
            ->streamedContent();

        $invoiceNumbers = $this->simpleCsvInvoiceNumbers($body);
        $this->assertContains((string) $invOwn->id, $invoiceNumbers);
        $this->assertNotContains((string) $invOther->id, $invoiceNumbers);
    }

    public function test_year_filter_works(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);

        $inv2025 = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 1_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
        $inv2025->timestamps = false;
        $inv2025->forceFill([
            'created_at' => Carbon::parse('2025-06-15 12:00:00'),
            'updated_at' => Carbon::parse('2025-06-15 12:00:00'),
        ])->save();

        $inv2026 = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 2_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
        $inv2026->timestamps = false;
        $inv2026->forceFill([
            'created_at' => Carbon::parse('2026-06-15 12:00:00'),
            'updated_at' => Carbon::parse('2026-06-15 12:00:00'),
        ])->save();

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), ['year' => 2026])
            ->assertOk()
            ->streamedContent();

        $invoiceNumbers = $this->simpleCsvInvoiceNumbers($body);
        $this->assertContains((string) $inv2026->id, $invoiceNumbers);
        $this->assertNotContains((string) $inv2025->id, $invoiceNumbers);
    }

    public function test_month_filter_works(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);

        $invMarch = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 3_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
        $invMarch->timestamps = false;
        $invMarch->forceFill([
            'created_at' => Carbon::parse('2026-03-10 12:00:00'),
            'updated_at' => Carbon::parse('2026-03-10 12:00:00'),
        ])->save();

        $invApril = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 4_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
        $invApril->timestamps = false;
        $invApril->forceFill([
            'created_at' => Carbon::parse('2026-04-10 12:00:00'),
            'updated_at' => Carbon::parse('2026-04-10 12:00:00'),
        ])->save();

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), ['year' => 2026, 'month' => 4])
            ->assertOk()
            ->streamedContent();

        $invoiceNumbers = $this->simpleCsvInvoiceNumbers($body);
        $this->assertContains((string) $invApril->id, $invoiceNumbers);
        $this->assertNotContains((string) $invMarch->id, $invoiceNumbers);
    }

    public function test_datev_export_uses_skr03_mapping(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 12_345,
            'status' => Rechnung::STATUS_BEZAHLT,
            'bezahlt_am' => now(),
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('8400', $body);
        $this->assertStringContainsString('1200', $body);
    }

    public function test_datev_export_uses_skr04_mapping(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 99_00,
            'status' => Rechnung::STATUS_BEZAHLT,
            'bezahlt_am' => now(),
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR04',
            ])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('4400', $body);
        $this->assertStringContainsString('1800', $body);
    }

    public function test_unpaid_invoice_is_marked_offen(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 7_777,
            'status' => Rechnung::STATUS_OFFEN,
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), ['year' => $year])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('OFFEN', $body);

        $datev = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('OFFEN', $datev);
    }

    public function test_paid_invoice_is_marked_bezahlt(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 8_888,
            'status' => Rechnung::STATUS_BEZAHLT,
            'bezahlt_am' => now(),
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), ['year' => $year])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('BEZAHLT', $body);

        $datev = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('BEZAHLT', $datev);
    }

    public function test_datev_export_excludes_storniert_invoices(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $invOpen = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 5_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
        $invStorno = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 9_999,
            'status' => Rechnung::STATUS_STORNIERT,
        ]);

        $year = (int) $invOpen->created_at->format('Y');

        $datev = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $ids = $this->datevCsvInvoiceIds($datev);
        $this->assertContains((string) $invOpen->id, $ids);
        $this->assertNotContains((string) $invStorno->id, $ids);
    }

    public function test_datev_csv_contains_netto_steuer_brutto_headers(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 1_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);

        $year = (int) now()->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $withoutBom = preg_replace('/^\xEF\xBB\xBF/', '', $body) ?? $body;
        $lines = preg_split('/\r\n|\r|\n/', $withoutBom);
        $this->assertIsArray($lines);
        $firstLine = $lines[0] ?? '';
        $this->assertNotSame('', $firstLine);
        $cells = str_getcsv($firstLine, ';');
        $this->assertSame('Netto', $cells[12] ?? null);
        $this->assertSame('Steuer', $cells[13] ?? null);
        $this->assertSame('Brutto', $cells[14] ?? null);
    }

    public function test_datev_csv_formats_net_steuer_from_source_data(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 11_900,
            'status' => Rechnung::STATUS_OFFEN,
            'source_data' => [
                'net_cent' => 10_000,
                'steuer_cent' => 1_900,
            ],
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $row = $this->datevCsvFirstDataRow($body);
        $this->assertNotNull($row);
        $this->assertSame('100,00', $row[12] ?? null);
        $this->assertSame('19,00', $row[13] ?? null);
        $this->assertSame('119,00', $row[14] ?? null);
    }

    public function test_datev_csv_leaves_net_steuer_empty_when_source_data_missing(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $inv = $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 11_900,
            'status' => Rechnung::STATUS_OFFEN,
            'source_data' => null,
        ]);

        $year = (int) $inv->created_at->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.datev-csv'), [
                'year' => $year,
                'chart_of_accounts' => 'SKR03',
            ])
            ->assertOk()
            ->streamedContent();

        $row = $this->datevCsvFirstDataRow($body);
        $this->assertNotNull($row);
        $this->assertSame('', $row[12] ?? null);
        $this->assertSame('', $row[13] ?? null);
        $this->assertSame('119,00', $row[14] ?? null);
    }

    public function test_csv_contains_utf8_bom_and_semicolon_separated_german_headers(): void
    {
        $owner = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $owner->mandant_id]);
        $this->makeInvoice($owner->mandant_id, $mieter->id, [
            'betrag_cent' => 1_000,
            'status' => Rechnung::STATUS_OFFEN,
        ]);

        $year = (int) now()->format('Y');

        $body = $this->actingAs($owner)
            ->post(route('export.accounting.simple-csv'), ['year' => $year])
            ->assertOk()
            ->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $body);

        $withoutBom = substr($body, 3);
        $lines = preg_split('/\r\n|\r|\n/', $withoutBom);
        $this->assertIsArray($lines);
        $firstLine = $lines[0];
        $this->assertNotNull($firstLine);
        $this->assertStringContainsString('Rechnungsnummer', $firstLine);
        $this->assertStringContainsString(';', $firstLine);
        $cells = str_getcsv($firstLine, ';');
        $this->assertGreaterThanOrEqual(12, count($cells));
    }

    /**
     * @return list<string>
     */
    private function datevCsvInvoiceIds(string $csvBody): array
    {
        $csvBody = preg_replace('/^\xEF\xBB\xBF/', '', $csvBody) ?? $csvBody;
        $lines = preg_split('/\r\n|\r|\n/', trim($csvBody));
        $this->assertNotFalse($lines);
        array_shift($lines);
        $ids = [];
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            $row = str_getcsv($line, ';');
            if (($row[6] ?? '') !== '') {
                $ids[] = (string) $row[6];
            }
        }

        return $ids;
    }

    /**
     * @return list<string>|null
     */
    private function datevCsvFirstDataRow(string $csvBody): ?array
    {
        $csvBody = preg_replace('/^\xEF\xBB\xBF/', '', $csvBody) ?? $csvBody;
        $lines = preg_split('/\r\n|\r|\n/', trim($csvBody));
        $this->assertNotFalse($lines);
        array_shift($lines);
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            return str_getcsv($line, ';');
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function simpleCsvInvoiceNumbers(string $csvBody): array
    {
        $csvBody = preg_replace('/^\xEF\xBB\xBF/', '', $csvBody) ?? $csvBody;
        $lines = preg_split('/\r\n|\r|\n/', trim($csvBody));
        $this->assertNotFalse($lines);
        array_shift($lines);
        $invoiceNumbers = [];
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            $row = str_getcsv($line, ';');
            if (($row[0] ?? '') !== '') {
                $invoiceNumbers[] = (string) $row[0];
            }
        }

        return $invoiceNumbers;
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeInvoice(int $mandantId, int $mieterId, array $overrides = []): Rechnung
    {
        /** @var Rechnung */
        return Rechnung::query()->create(array_merge([
            'mandant_id' => $mandantId,
            'mieter_id' => $mieterId,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 10_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ], $overrides));
    }
}
